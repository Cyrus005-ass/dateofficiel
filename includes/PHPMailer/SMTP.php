<?php
/**
 * PHPMailer RFC821 SMTP email transport class.
 * Simplified version for Shopping Date project.
 * Full version: https://github.com/PHPMailer/PHPMailer
 * @license LGPL 2.1
 */
namespace PHPMailer\PHPMailer;

class SMTP
{
    const VERSION = '6.8.0';
    const LE = "\r\n";
    const DEFAULT_PORT = 25;
    const DEFAULT_SMTP_PORT = 25;
    const MAX_LINE_LENGTH = 998;
    const MAX_REPLY_LENGTH = 512;
    const DEBUG_OFF = 0;
    const DEBUG_CLIENT = 1;
    const DEBUG_SERVER = 2;
    const DEBUG_CONNECTION = 3;
    const DEBUG_LOWLEVEL = 4;

    public $Version = '6.8.0';
    public $SMTP_PORT = 25;
    public $CRLF = "\r\n";
    public $do_debug = self::DEBUG_OFF;
    public $Debugoutput = 'echo';
    public $do_verp = false;
    public $Timeout = 300;
    public $Timelimit = 300;
    public $lastTransaction = 0;
    public $edebug_level = self::DEBUG_OFF;

    protected $smtp_conn;
    protected $error = ['error' => '', 'detail' => '', 'smtp_code' => '', 'smtp_code_ex' => ''];
    protected $helo_rply = null;
    protected $server_caps = null;
    protected $last_reply = '';

    public function connect($host, $port = null, $timeout = 30, $options = [])
    {
        static $streamok;
        if (is_null($streamok)) {
            $streamok = function_exists('stream_socket_client');
        }
        $this->setError('');
        if ($this->connected()) {
            $this->setError('Already connected to a server');
            return false;
        }
        if (empty($port)) {
            $port = self::DEFAULT_PORT;
        }
        $host = trim($host);
        if ($streamok) {
            $errno = 0;
            $errstr = '';
            if (!array_key_exists('ssl', $options) && strpos($host, 'ssl://') === 0) {
                array_unshift($options, []);
            }
            $socket_context = stream_context_create($options);
            set_error_handler([$this, 'errorHandler']);
            $this->smtp_conn = stream_socket_client(
                $host . ':' . $port,
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $socket_context
            );
            restore_error_handler();
        } else {
            set_error_handler([$this, 'errorHandler']);
            $this->smtp_conn = fsockopen($host, $port, $errno, $errstr, $timeout);
            restore_error_handler();
        }
        if (!is_resource($this->smtp_conn)) {
            $this->setError(
                'Failed to connect to server',
                '',
                (string) $errno,
                (string) $errstr
            );
            $this->edebug('SMTP ERROR: ' . $this->error['error'] . ': ' . $errstr, self::DEBUG_CLIENT);
            return false;
        }
        $this->edebug('Connection: opened', self::DEBUG_CONNECTION);
        stream_set_timeout($this->smtp_conn, $timeout, 0);
        $announce = $this->get_lines();
        $this->edebug('SERVER -> CLIENT: ' . $announce, self::DEBUG_SERVER);
        return true;
    }

    public function startTLS()
    {
        if (!$this->sendCommand('STARTTLS', 'STARTTLS', 220)) {
            return false;
        }
        $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
            $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
        }
        set_error_handler([$this, 'errorHandler']);
        $crypto_ok = stream_socket_enable_crypto(
            $this->smtp_conn,
            true,
            $crypto_method
        );
        restore_error_handler();
        return (bool) $crypto_ok;
    }

    public function authenticate(
        $username,
        $password,
        $authtype = null,
        $OAuth = null
    ) {
        if (!$this->server_caps) {
            $this->setError('Authentication is not allowed before HELO/EHLO');
            return false;
        }
        if (array_key_exists('EHLO', $this->server_caps)) {
            if (!array_key_exists('AUTH', $this->server_caps)) {
                $this->setError('Authentication is not allowed at this stage');
                return false;
            }
            $this->server_caps['AUTH'];
        }
        if (empty($authtype)) {
            foreach (['CRAM-MD5', 'LOGIN', 'PLAIN', 'XOAUTH2'] as $method) {
                if (in_array($method, $this->server_caps['AUTH'])) {
                    $authtype = $method;
                    break;
                }
            }
            if (empty($authtype)) {
                $this->setError('No supported authentication methods found');
                return false;
            }
            self::edebug('AUTH: Using ' . $authtype, self::DEBUG_CLIENT);
        }
        $authtype = strtoupper($authtype);
        switch ($authtype) {
            case 'PLAIN':
                if (!$this->sendCommand('AUTH', 'AUTH PLAIN ' . base64_encode("\0" . $username . "\0" . $password), 235)) {
                    return false;
                }
                break;
            case 'LOGIN':
                if (!$this->sendCommand('AUTH', 'AUTH LOGIN', 334)) {
                    return false;
                }
                if (!$this->sendCommand('Username', base64_encode($username), 334)) {
                    return false;
                }
                if (!$this->sendCommand('Password', base64_encode($password), 235)) {
                    return false;
                }
                break;
            case 'CRAM-MD5':
                if (!$this->sendCommand('AUTH CRAM-MD5', 'AUTH CRAM-MD5', 334)) {
                    return false;
                }
                $challenge = base64_decode(substr($this->last_reply, 4));
                $response = $username . ' ' . $this->hmac($challenge, $password);
                if (!$this->sendCommand('Username', base64_encode($response), 235)) {
                    return false;
                }
                break;
            default:
                $this->setError("Authentication method \"$authtype\" is not supported");
                return false;
        }
        return true;
    }

    protected function hmac($data, $key)
    {
        if (function_exists('hash_hmac')) {
            return hash_hmac('md5', $data, $key);
        }
        $bytelen = 64;
        if (strlen($key) > $bytelen) {
            $key = pack('H*', md5($key));
        }
        $key = str_pad($key, $bytelen, chr(0x00));
        $ipad = str_pad('', $bytelen, chr(0x36));
        $opad = str_pad('', $bytelen, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;
        return md5($k_opad . pack('H*', md5($k_ipad . $data)));
    }

    public function connected()
    {
        if (is_resource($this->smtp_conn)) {
            $sock_status = stream_get_meta_data($this->smtp_conn);
            if ($sock_status['eof']) {
                $this->edebug('SMTP NOTICE: EOF caught while checking if connected', self::DEBUG_CLIENT);
                $this->close();
                return false;
            }
            return true;
        }
        return false;
    }

    public function close()
    {
        $this->setError('');
        $this->server_caps = null;
        $this->helo_rply = null;
        if (is_resource($this->smtp_conn)) {
            fclose($this->smtp_conn);
            $this->smtp_conn = null;
            $this->edebug('Connection: closed', self::DEBUG_CONNECTION);
        }
    }

    public function data($msg_data)
    {
        if (!$this->sendCommand('DATA', 'DATA', 354)) {
            return false;
        }
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $msg_data));
        $field = substr($lines[0], 0, strpos($lines[0], ':'));
        $in_headers = true;
        $max_line_length = 998;
        $byteswritten = 0;
        foreach ($lines as $line) {
            $lines_out = [];
            if ($in_headers && empty($line)) {
                $in_headers = false;
            }
            while (isset($line[self::MAX_LINE_LENGTH])) {
                $pos = strrpos(substr($line, 0, self::MAX_LINE_LENGTH), ' ');
                if (!$pos) {
                    $pos = self::MAX_LINE_LENGTH - 1;
                    $lines_out[] = substr($line, 0, $pos);
                    $line = substr($line, $pos);
                } else {
                    $lines_out[] = substr($line, 0, $pos);
                    $line = substr($line, $pos + 1);
                }
                if ($in_headers) {
                    $lines_out[] = "\t";
                }
            }
            $lines_out[] = $line;
            foreach ($lines_out as $line_out) {
                if (!empty($line_out) && '.' === $line_out[0]) {
                    $line_out = '.' . $line_out;
                }
                $this->client_send($line_out . static::LE, 'DATA');
            }
        }
        return $this->sendCommand('DATA END', '.', 250);
    }

    public function hello($host = '')
    {
        return $this->sendHello('EHLO', $host) || $this->sendHello('HELO', $host);
    }

    protected function sendHello($hello, $host)
    {
        $noerror = $this->sendCommand($hello, $hello . ' ' . $host, 250);
        $this->helo_rply = $this->last_reply;
        if ($noerror) {
            $this->parseHelloFields($hello);
        } else {
            $this->server_caps = null;
        }
        return $noerror;
    }

    protected function parseHelloFields($type)
    {
        $this->server_caps = [];
        $lines = explode("\n", $this->helo_rply);
        foreach ($lines as $n => $s) {
            $s = trim(substr($s, 4));
            if (!$s) {
                continue;
            }
            $fields = explode(' ', $s);
            if ($fields) {
                if (!$n) {
                    $name = $type;
                    $fields = $fields[0];
                } else {
                    $name = array_shift($fields);
                    switch ($name) {
                        case 'SIZE':
                            $fields = ($fields ? $fields[0] : 0);
                            break;
                        case 'AUTH':
                            if (!is_array($fields)) {
                                $fields = [];
                            }
                            break;
                        default:
                            $fields = true;
                    }
                }
                $this->server_caps[$name] = $fields;
            }
        }
    }

    public function mail($from)
    {
        $useVerp = ($this->do_verp ? ' XVERP' : '');
        return $this->sendCommand(
            'MAIL FROM',
            'MAIL FROM:<' . $from . '>' . $useVerp,
            250
        );
    }

    public function quit($close_on_error = true)
    {
        $noerror = $this->sendCommand('QUIT', 'QUIT', 221);
        $err = $this->error;
        if ($noerror || $close_on_error) {
            $this->close();
            $this->error = $err;
        }
        return $noerror;
    }

    public function recipient($address, $dsn = '')
    {
        if (empty($dsn)) {
            $rcpt = 'RCPT TO:<' . $address . '>';
        } else {
            $rcpt = 'RCPT TO:<' . $address . '> NOTIFY=' . $dsn;
        }
        return $this->sendCommand('RCPT TO', $rcpt, [250, 251]);
    }

    public function reset()
    {
        return $this->sendCommand('RSET', 'RSET', 250);
    }

    public function sendCommand($command, $commandstring, $expect)
    {
        if (!$this->connected()) {
            $this->setError("Called $command without being connected");
            return false;
        }
        $commandstring = static::stripLE($commandstring);
        $this->client_send($commandstring . static::LE, $command);
        $this->last_reply = $this->get_lines();
        $responseCode = (int) substr($this->last_reply, 0, 3);
        if ($responseCode === 0) {
            $detail = $this->last_reply;
            if (empty($detail)) {
                $detail = 'Server returned empty response.';
            }
            $this->setError("$command: " . $detail);
            return false;
        }
        $in_array = is_array($expect);
        if (($in_array && !in_array($responseCode, $expect)) || (!$in_array && $responseCode !== $expect)) {
            $this->setError("$command: " . substr($this->last_reply, 4));
            $this->edebug(
                'SERVER -> CLIENT: ' . $this->last_reply,
                self::DEBUG_SERVER
            );
            return false;
        }
        $this->edebug('SERVER -> CLIENT: ' . $this->last_reply, self::DEBUG_SERVER);
        return true;
    }

    public function client_send($data, $command = '')
    {
        $bad_commands = ['USER', 'PASS', 'AUTH'];
        if ($this->do_debug >= self::DEBUG_CLIENT && ($command === '' || !in_array($command, $bad_commands))) {
            $this->edebug('CLIENT -> SERVER: ' . $data, self::DEBUG_CLIENT);
        }
        set_error_handler([$this, 'errorHandler']);
        $result = fwrite($this->smtp_conn, $data);
        restore_error_handler();
        $this->lastTransaction++;
        return $result;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getServerCaps()
    {
        return $this->server_caps;
    }

    public function getServerExt($name)
    {
        if (!$this->server_caps) {
            $this->setError('No HELO/EHLO was sent');
            return null;
        }
        if (!array_key_exists($name, $this->server_caps)) {
            if ('HELO' === $name) {
                return $this->server_caps['EHLO'] ?? true;
            }
            return null;
        }
        return $this->server_caps[$name];
    }

    public function getLastReply()
    {
        return $this->last_reply;
    }

    protected function get_lines()
    {
        if (!is_resource($this->smtp_conn)) {
            return '';
        }
        $data = '';
        $endtime = 0;
        stream_set_timeout($this->smtp_conn, $this->Timeout);
        if ($this->Timelimit > 0) {
            $endtime = time() + $this->Timelimit;
        }
        $selR = [$this->smtp_conn];
        $selW = null;
        while (is_resource($this->smtp_conn) && !feof($this->smtp_conn)) {
            set_error_handler([$this, 'errorHandler']);
            $n = stream_select($selR, $selW, $selW, $this->Timelimit);
            restore_error_handler();
            if ($n === false) {
                $message = $this->getError()['detail'];
                $this->edebug('SMTP -> get_lines(): select failed (' . $message . ')', self::DEBUG_LOWLEVEL);
                break;
            }
            if ($n === 0) {
                $this->edebug('SMTP -> get_lines(): select timed out in waiting for data', self::DEBUG_LOWLEVEL);
                break;
            }
            $str = @fgets($this->smtp_conn, 515);
            $this->edebug('SMTP INBOUND: "' . trim($str) . '"', self::DEBUG_LOWLEVEL);
            $data .= $str;
            if (!isset($str[3]) || ($str[3] === '-' && $str[3] !== ' ')) {
                $this->edebug('SMTP -> get_lines(): ' . $data, self::DEBUG_LOWLEVEL);
                break;
            }
            $info = stream_get_meta_data($this->smtp_conn);
            if ($info['timed_out']) {
                $this->edebug('SMTP -> get_lines(): stream timed out waiting for data', self::DEBUG_LOWLEVEL);
                break;
            }
            if ($endtime && time() > $endtime) {
                $this->edebug('SMTP -> get_lines(): Timed out', self::DEBUG_LOWLEVEL);
                break;
            }
        }
        return $data;
    }

    public function setVerp($enabled = false)
    {
        $this->do_verp = $enabled;
    }

    public function setDebugLevel($level = 0)
    {
        $this->do_debug = $level;
    }

    public function setDebugOutput($method = 'echo')
    {
        $this->Debugoutput = $method;
    }

    public function setError($message, $detail = '', $smtp_code = '', $smtp_code_ex = '')
    {
        $this->error = [
            'error' => $message,
            'detail' => $detail,
            'smtp_code' => $smtp_code,
            'smtp_code_ex' => $smtp_code_ex,
        ];
    }

    protected function edebug($str, $level = 0)
    {
        if ($level > $this->do_debug) {
            return;
        }
        $prefix = '';
        switch ($this->do_debug) {
            case self::DEBUG_LOWLEVEL:
            case self::DEBUG_CONNECTION:
            case self::DEBUG_SERVER:
            case self::DEBUG_CLIENT:
                $prefix = gmdate('Y-m-d H:i:s') . "\t";
        }
        if (is_callable($this->Debugoutput) && !in_array($this->Debugoutput, ['error_log', 'html', 'echo'])) {
            call_user_func($this->Debugoutput, $str, $level);
            return;
        }
        switch ($this->Debugoutput) {
            case 'error_log':
                error_log($str);
                break;
            case 'html':
                echo gmdate('Y-m-d H:i:s') . ' ' . htmlspecialchars(preg_replace('/[\r\n]+/', '', $str)) . "<br>\n";
                break;
            case 'echo':
            default:
                echo $prefix . $str . "\n";
        }
    }

    public function errorHandler($errno, $errmsg, $errfile, $errline)
    {
        $notice = 'Connection failed.';
        $this->setError(
            $notice,
            $errmsg,
            (string) $errno
        );
    }

    public static function parseAddresses($addrstr, $useimap = true)
    {
        $addresses = [];
        if ($useimap && function_exists('imap_rfc822_parse_adrlist')) {
            $list = imap_rfc822_parse_adrlist($addrstr, '');
            foreach ($list as $address) {
                if (
                    '.SYNTAX-ERROR.' !== $address->host &&
                    isset($address->mailbox, $address->host)
                ) {
                    imap_utf8($address->personal);
                    $addresses[] = [
                        'name' => imap_utf8($address->personal),
                        'address' => $address->mailbox . '@' . $address->host,
                    ];
                }
            }
        } else {
            $list = explode(',', $addrstr);
            foreach ($list as $address) {
                $address = trim($address);
                if (strpos($address, '<') !== false) {
                    $name = trim(str_replace('"', '', substr($address, 0, strpos($address, '<'))));
                    $email = trim(str_replace('>', '', substr($address, strpos($address, '<') + 1)));
                    $addresses[] = ['name' => $name, 'address' => $email];
                } else {
                    $addresses[] = ['name' => '', 'address' => $address];
                }
            }
        }
        return $addresses;
    }

    public static function rfcDate()
    {
        $tz = @date('Z');
        $tzs = ($tz < 0 ? '-' : '+');
        $tz = abs($tz);
        $tz = (int) ($tz / 3600) * 100 + ($tz % 3600) / 60;
        $result = sprintf('%s %s%04d', date('D, j M Y H:i:s'), $tzs, $tz);
        return $result;
    }

    public static function stripLE($string)
    {
        return rtrim($string, "\r\n");
    }

    public function getLastTransactionID()
    {
        $reply = $this->getLastReply();
        if (preg_match('/[0-9]{3}\s+ok\s+.+queued\s+as\s+(.+)/i', $reply, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
