<?php
/**
 * PHPMailer - Full-featured email creation and transfer class for PHP.
 * Simplified core version for Shopping Date project.
 * Full version: https://github.com/PHPMailer/PHPMailer
 * @license LGPL 2.1
 */
namespace PHPMailer\PHPMailer;

class PHPMailer
{
    const CHARSET_ASCII  = 'us-ascii';
    const CHARSET_ISO88591 = 'iso-8859-1';
    const CHARSET_UTF8   = 'utf-8';
    const CONTENT_TYPE_PLAINTEXT = 'text/plain';
    const CONTENT_TYPE_TEXT_CALENDAR = 'text/calendar';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const CONTENT_TYPE_MULTIPART_ALTERNATIVE = 'multipart/alternative';
    const CONTENT_TYPE_MULTIPART_MIXED = 'multipart/mixed';
    const CONTENT_TYPE_MULTIPART_RELATED = 'multipart/related';
    const ENCODING_7BIT   = '7bit';
    const ENCODING_8BIT   = '8bit';
    const ENCODING_BASE64 = 'base64';
    const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';
    const ENCODING_RAW    = '';
    const ENCRYPTION_STARTTLS = 'tls';
    const ENCRYPTION_SMTPS    = 'ssl';

    public $Version = '6.8.0';
    public $Priority;
    public $CharSet = self::CHARSET_UTF8;
    public $ContentType = self::CONTENT_TYPE_TEXT_PLAIN;
    public $Encoding = self::ENCODING_8BIT;
    public $ErrorInfo = '';
    public $From = 'root@localhost';
    public $FromName = 'Root User';
    public $Sender = '';
    public $ReturnPath = '';
    public $Subject = '';
    public $Body = '';
    public $AltBody = '';
    public $Ical = '';
    public $MIMEBody = '';
    public $MIMEHeader = '';
    public $mailHeader = '';
    public $WordWrap = 0;
    public $Mailer = 'mail';
    public $Sendmail = '/usr/sbin/sendmail';
    public $UseSendmailOptions = true;
    public $PluginDir = '';
    public $ConfirmReadingTo = '';
    public $Hostname = '';
    public $MessageID = '';
    public $MessageDate = '';
    public $Host = 'localhost';
    public $Port = 25;
    public $Helo = '';
    public $SMTPSecure = '';
    public $SMTPAutoTLS = true;
    public $SMTPAuth = false;
    public $SMTPAuthType = '';
    public $SMTPOptions = [];
    public $Username = '';
    public $Password = '';
    public $OAuth;
    public $Timeout = 300;
    public $dsn = '';
    public $SMTPDebug = 0;
    public $Debugoutput = 'echo';
    public $SMTPKeepAlive = false;
    public $SingleTo = false;
    public $do_verp = false;
    public $AllowEmpty = false;
    public $XMailer = '';
    public $exceptions = false;
    public $DKIM_selector = '';
    public $DKIM_identity = '';
    public $DKIM_passphrase = '';
    public $DKIM_domain = '';
    public $DKIM_copyHeaderFields = true;
    public $DKIM_extraHeaders = [];
    public $DKIM_private = '';
    public $DKIM_private_string = '';
    public $action_function = '';
    public $LastMessageID = '';
    public $ContentType_text_plain = self::CONTENT_TYPE_PLAINTEXT;

    protected $to = [];
    protected $cc = [];
    protected $bcc = [];
    protected $ReplyTo = [];
    protected $all_recipients = [];
    protected $RecipientsQueue = [];
    protected $ReplyToQueue = [];
    protected $attachment = [];
    protected $CustomHeader = [];
    protected $lastMessageID = '';
    protected $message_type = '';
    protected $boundary = [];
    protected $language = [];
    protected $error_count = 0;
    protected $sign_cert_file = '';
    protected $sign_key_file = '';
    protected $sign_extracerts_file = '';
    protected $sign_key_pass = '';
    protected $exceptions_is_set = false;
    protected $uniqueid = '';
    /** @var SMTP */
    protected $smtp;

    public function __construct($exceptions = null)
    {
        if (null !== $exceptions) {
            $this->exceptions = (bool) $exceptions;
        }
        // Pick up the server hostname if not set
        if (empty($this->Hostname)) {
            if (gethostname() !== false) {
                $this->Hostname = gethostname();
            }
        }
    }

    public function __destruct()
    {
        $this->smtpClose();
    }

    private function mailPassthru($to, $subject, $body, $header, $params)
    {
        if (ini_get('safe_mode') || !($this->UseSendmailOptions)) {
            $result = @mail($to, $subject, $body, $header);
        } else {
            $result = @mail($to, $subject, $body, $header, $params);
        }
        return $result;
    }

    protected function edebug($str)
    {
        if ($this->SMTPDebug <= 0) {
            return;
        }
        if (is_callable($this->Debugoutput) && !in_array($this->Debugoutput, ['error_log', 'html', 'echo'])) {
            call_user_func($this->Debugoutput, $str, $this->SMTPDebug);
            return;
        }
        switch ($this->Debugoutput) {
            case 'error_log':
                error_log($str);
                break;
            case 'html':
                echo htmlspecialchars(preg_replace('/[\r\n]+/', '', $str), ENT_QUOTES) . "<br>\n";
                break;
            case 'echo':
            default:
                $str = preg_replace('/(\r\n|\r|\n)/ms', "\n", $str);
                echo gmdate('Y-m-d H:i:s') . "\t" . trim($str) . "\n";
        }
    }

    public function isHTML($isHtml = true)
    {
        if ($isHtml) {
            $this->ContentType = static::CONTENT_TYPE_TEXT_HTML;
        } else {
            $this->ContentType = static::CONTENT_TYPE_PLAINTEXT;
        }
    }

    public function isSMTP()
    {
        $this->Mailer = 'smtp';
    }

    public function isMail()
    {
        $this->Mailer = 'mail';
    }

    public function isSendmail()
    {
        $ini_sendmail_path = ini_get('sendmail_path');
        if (!strstr($ini_sendmail_path, 'sendmail')) {
            $this->Sendmail = '/usr/sbin/sendmail';
        } else {
            $this->Sendmail = $ini_sendmail_path;
        }
        $this->Mailer = 'sendmail';
    }

    public function isQmail()
    {
        if (stristr(ini_get('sendmail_path'), 'qmail')) {
            $this->Sendmail = '/var/qmail/bin/sendmail';
        }
        $this->Mailer = 'sendmail';
    }

    public function addAddress($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('to', $address, $name);
    }

    public function addCC($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('cc', $address, $name);
    }

    public function addBCC($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('bcc', $address, $name);
    }

    public function addReplyTo($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('Reply-To', $address, $name);
    }

    protected function addOrEnqueueAnAddress($kind, $address, $name)
    {
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name));
        $pos = strrpos($address, '@');
        if (false === $pos) {
            $error_message = sprintf('%s (%s): %s', $this->lang('invalid_address'), $kind, $address);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }
            return false;
        }
        $params = [$kind, $address, $name];
        if ($this->do_verp) {
            return call_user_func_array([$this, 'addAnAddress'], $params);
        }
        if (!array_key_exists($address, $this->all_recipients)) {
            return call_user_func_array([$this, 'addAnAddress'], $params);
        }
        if ('Reply-To' !== $kind) {
            return false;
        }
        if (!array_key_exists($address, $this->ReplyToQueue)) {
            $this->ReplyToQueue[$address] = $params;
            return true;
        }
        return false;
    }

    protected function addAnAddress($kind, $address, $name = '')
    {
        if (!in_array($kind, ['to', 'cc', 'bcc', 'Reply-To'])) {
            $error_message = sprintf('%s: %s', $this->lang('Invalid recipient kind'), $kind);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }
            return false;
        }
        if (!static::validateAddress($address)) {
            $error_message = sprintf('%s (%s): %s', $this->lang('invalid_address'), $kind, $address);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }
            return false;
        }
        if ('Reply-To' !== $kind) {
            if (!array_key_exists(strtolower($address), $this->all_recipients)) {
                $this->$kind[] = [$address, $name];
                $this->all_recipients[strtolower($address)] = true;
            }
            return true;
        }
        if (!array_key_exists(strtolower($address), $this->ReplyTo)) {
            $this->ReplyTo[strtolower($address)] = [$address, $name];
            return true;
        }
        return false;
    }

    public function setFrom($address, $name = '', $auto = true)
    {
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name));
        if (!static::validateAddress($address)) {
            $error_message = sprintf('%s (From): %s', $this->lang('invalid_address'), $address);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }
            return false;
        }
        $this->From = $address;
        $this->FromName = $name;
        if ($auto && empty($this->Sender)) {
            $this->Sender = $address;
        }
        return true;
    }

    public function getToAddresses()
    {
        return $this->to;
    }

    public function getCcAddresses()
    {
        return $this->cc;
    }

    public function getBccAddresses()
    {
        return $this->bcc;
    }

    public function getReplyToAddresses()
    {
        return $this->ReplyTo;
    }

    public function getAllRecipientAddresses()
    {
        return $this->all_recipients;
    }

    protected function preSend()
    {
        if ('smtp' === $this->Mailer || 'mail' === $this->Mailer || 'sendmail' === $this->Mailer || 'qmail' === $this->Mailer) {
            // nothing
        }
        $this->error_count = 0;
        $this->mailHeader = '';
        if (count($this->to) + count($this->cc) + count($this->bcc) < 1) {
            throw new Exception($this->lang('provide_address'), self::STOP_CRITICAL);
        }
        foreach (array_merge($this->RecipientsQueue, $this->ReplyToQueue) as $params) {
            $this->addAnAddress(...$params);
        }
        if ('' === $this->Body) {
            throw new Exception($this->lang('empty_message'), self::STOP_CRITICAL);
        }
        if (strpos($this->From, '@') === false) {
            throw new Exception($this->lang('provide_address'), self::STOP_CRITICAL);
        }
        if (empty($this->uniqueid)) {
            $this->uniqueid = $this->generateId();
        }
        $this->MIMEHeader = '';
        $this->MIMEBody = $this->createBody();
        if ('' === $this->MIMEHeader) {
            $this->MIMEHeader = $this->createHeader();
        }
        $this->mailHeader = '';
        return true;
    }

    public function send()
    {
        try {
            if (!$this->preSend()) {
                return false;
            }
            return $this->postSend();
        } catch (Exception $exc) {
            $this->mailHeader = '';
            $this->setError($exc->getMessage());
            $this->edebug($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }
            return false;
        }
    }

    protected function postSend()
    {
        try {
            switch ($this->Mailer) {
                case 'sendmail':
                case 'qmail':
                    return $this->sendmailSend($this->MIMEHeader, $this->MIMEBody);
                case 'smtp':
                    return $this->smtpSend($this->MIMEHeader, $this->MIMEBody);
                case 'mail':
                    return $this->mailSend($this->MIMEHeader, $this->MIMEBody);
                default:
                    $sendMethod = $this->Mailer . 'Send';
                    if (callable($sendMethod)) {
                        return call_user_func($sendMethod, $this->MIMEHeader, $this->MIMEBody);
                    }
                    return $this->mailSend($this->MIMEHeader, $this->MIMEBody);
            }
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            $this->edebug($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }
            return false;
        }
    }

    protected function sendmailSend($header, $body)
    {
        if ($this->Sender !== '' && static::validateAddress($this->Sender)) {
            if ('qmail' === $this->Mailer) {
                $sendmailFmt = '%s "%s"';
            } else {
                $sendmailFmt = '%s -oi -f%s';
            }
        } else {
            if ('qmail' === $this->Mailer) {
                $sendmailFmt = '%s';
            } else {
                $sendmailFmt = '%s -oi';
            }
        }
        $sendmail = sprintf($sendmailFmt, escapeshellcmd($this->Sendmail), $this->Sender);
        if ($this->SingleTo) {
            foreach ($this->SingleToArray as $toAddr) {
                $mail = @popen($sendmail . ' ' . escapeshellarg($toAddr), 'w');
                if (!$mail) {
                    throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
                }
                fputs($mail, $header);
                fputs($mail, $body);
                $result = pclose($mail);
                $addrinfo = static::parseAddresses($toAddr);
                $this->doCallback(
                    ($result === 0),
                    [[$addrinfo[0]['address'], $addrinfo[0]['name']]],
                    $this->cc, $this->bcc, $this->Subject, $body, $this->From, []
                );
                if (0 !== $result) {
                    throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
                }
            }
        } else {
            $mail = @popen($sendmail, 'w');
            if (!$mail) {
                throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
            fputs($mail, $header);
            fputs($mail, $body);
            $result = pclose($mail);
            $this->doCallback(($result === 0), $this->to, $this->cc, $this->bcc, $this->Subject, $body, $this->From, []);
            if (0 !== $result) {
                throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
        }
        return true;
    }

    protected function mailSend($header, $body)
    {
        $toArr = [];
        foreach ($this->to as $toaddr) {
            $toArr[] = $this->addrFormat($toaddr);
        }
        $to = implode(', ', $toArr);
        $params = null;
        if (!empty($this->Sender) && static::validateAddress($this->Sender)) {
            $params = '-f' . $this->Sender;
        }
        if ($this->Sender !== '' && !ini_get('safe_mode') && $this->UseSendmailOptions) {
            $old_from = ini_get('sendmail_from');
            ini_set('sendmail_from', $this->Sender);
        }
        $result = false;
        if ($this->SingleTo && count($toArr) > 1) {
            foreach ($toArr as $toAddr) {
                $result = $this->mailPassthru($toAddr, $this->Subject, $body, $header, $params);
                $addrinfo = static::parseAddresses($toAddr);
                $this->doCallback(
                    $result,
                    [[$addrinfo[0]['address'], $addrinfo[0]['name']]],
                    $this->cc, $this->bcc, $this->Subject, $body, $this->From, []
                );
            }
        } else {
            $result = $this->mailPassthru($to, $this->Subject, $body, $header, $params);
            $this->doCallback($result, $this->to, $this->cc, $this->bcc, $this->Subject, $body, $this->From, []);
        }
        if (isset($old_from)) {
            ini_set('sendmail_from', $old_from);
        }
        if (!$result) {
            throw new Exception($this->lang('instantiate'), self::STOP_CRITICAL);
        }
        return true;
    }

    public function getSMTPInstance()
    {
        if (!is_object($this->smtp)) {
            $this->smtp = new SMTP();
        }
        return $this->smtp;
    }

    public function setSMTPInstance(SMTP $smtp)
    {
        $this->smtp = $smtp;
        return $this;
    }

    protected function smtpConnect($options = [])
    {
        if (null === $this->smtp) {
            $this->smtp = $this->getSMTPInstance();
        }
        if ($this->smtp->connected()) {
            return true;
        }
        $this->smtp->setTimeout($this->Timeout ?? 300);
        $this->smtp->setDebugLevel($this->SMTPDebug);
        $this->smtp->setDebugOutput($this->Debugoutput);
        $this->smtp->setVerp($this->do_verp);
        $hosts = explode(';', $this->Host);
        $lastexception = null;

        foreach ($hosts as $hostentry) {
            $hostinfo = [];
            if (!preg_match('/^((ssl|tls):\/\/)*([a-zA-Z0-9\.-]*|\[[a-fA-F0-9:]+\]):?(\d+)?$/', trim($hostentry), $hostinfo)) {
                $this->edebug($this->lang('connect_host') . ' ' . $hostentry);
                continue;
            }
            $prefix = '';
            $secure = $this->SMTPSecure;
            $tls = (static::ENCRYPTION_STARTTLS === $this->SMTPSecure);
            if ('ssl' === $hostinfo[2] || ('' === $hostinfo[2] && static::ENCRYPTION_SMTPS === $this->SMTPSecure)) {
                $prefix = 'ssl://';
                $tls = false;
                $secure = static::ENCRYPTION_SMTPS;
            } elseif ('tls' === $hostinfo[2]) {
                $tls = true;
                $secure = static::ENCRYPTION_STARTTLS;
            }
            $sslext = defined('OPENSSL_ALGO_SHA256');
            if (static::ENCRYPTION_STARTTLS === $secure || static::ENCRYPTION_SMTPS === $secure) {
                $sslext = true;
            }
            $host = $hostinfo[3];
            $port = $this->Port;
            $tport = (int) $hostinfo[4];
            if ($tport > 0 && $tport < 65536) {
                $port = $tport;
            }
            if ($this->smtp->connect($prefix . $host, $port, $this->Timeout, $options)) {
                try {
                    if ($this->Helo) {
                        $hello = $this->Helo;
                    } else {
                        $hello = $this->serverHostname();
                    }
                    $this->smtp->hello($hello);
                    if ($this->SMTPAutoTLS && $sslext && 'ssl' !== $secure && $this->smtp->getServerExt('STARTTLS')) {
                        $tls = true;
                    }
                    if ($tls) {
                        if (!$this->smtp->startTLS()) {
                            throw new Exception($this->lang('connect_host'));
                        }
                        $this->smtp->hello($hello);
                    }
                    if ($this->SMTPAuth) {
                        if (!$this->smtp->authenticate($this->Username, $this->Password, $this->SMTPAuthType, $this->OAuth)) {
                            throw new Exception($this->lang('authenticate'));
                        }
                    }
                    return true;
                } catch (Exception $exc) {
                    $lastexception = $exc;
                    $this->edebug($exc->getMessage());
                    $this->smtp->quit();
                }
            }
        }
        if ($this->exceptions && null !== $lastexception) {
            throw $lastexception;
        }
        return false;
    }

    protected function smtpSend($header, $body)
    {
        $bad_rcpt = [];
        if (!$this->smtpConnect($this->SMTPOptions)) {
            throw new Exception($this->lang('smtp_connect_failed'), self::STOP_CRITICAL);
        }
        if ('' !== $this->Sender && static::validateAddress($this->Sender)) {
            $smtp_from = $this->Sender;
        } else {
            $smtp_from = $this->From;
        }
        if (!$this->smtp->mail($smtp_from)) {
            $this->setError($this->lang('from_failed') . $smtp_from . ' : ' . implode(',', $this->smtp->getError()));
            throw new Exception($this->ErrorInfo, self::STOP_CRITICAL);
        }
        $callbacks = [];
        foreach ([$this->to, $this->cc, $this->bcc] as $togroup) {
            foreach ($togroup as $to) {
                if (!$this->smtp->recipient($to[0], $this->dsn)) {
                    $error = $this->smtp->getError();
                    $bad_rcpt[] = ['to' => $to[0], 'error' => $error['detail']];
                    $isSent = false;
                } else {
                    $isSent = true;
                }
                $callbacks[] = ['issent' => $isSent, 'to' => $to[0], 'name' => $to[1]];
            }
        }
        if (count($bad_rcpt) > 0 && count($bad_rcpt) === count($this->to)) {
            $errstr = '';
            foreach ($bad_rcpt as $bad) {
                $errstr .= $bad['to'] . ': ' . $bad['error'];
            }
            throw new Exception($this->lang('recipients_failed') . $errstr, self::STOP_CRITICAL);
        }
        if (!$this->smtp->data($header . $body)) {
            throw new Exception($this->lang('data_not_accepted'), self::STOP_CRITICAL);
        }
        $this->LastMessageID = $this->smtp->getLastTransactionID();
        if ($this->SMTPKeepAlive) {
            $this->smtp->reset();
        } else {
            $this->smtp->quit();
            $this->smtp->close();
        }
        foreach ($callbacks as $cb) {
            $this->doCallback($cb['issent'], [[$cb['to'], $cb['name']]], [], [], $this->Subject, $body, $this->From, []);
        }
        if (count($bad_rcpt) > 0) {
            $errstr = '';
            foreach ($bad_rcpt as $bad) {
                $errstr .= $bad['to'] . ': ' . $bad['error'];
            }
            throw new Exception($this->lang('recipients_failed') . $errstr, self::STOP_CRITICAL);
        }
        return true;
    }

    public function smtpClose()
    {
        if ((null !== $this->smtp) && $this->smtp->connected()) {
            $this->smtp->quit();
            $this->smtp->close();
        }
    }

    protected function createHeader()
    {
        $result = '';
        $result .= $this->headerLine('Date', $this->MessageDate === '' ? self::rfcDate() : $this->MessageDate);
        if ($this->SingleTo) {
            if ('mail' !== $this->Mailer) {
                foreach ($this->to as $toaddr) {
                    $this->SingleToArray[] = $this->addrFormat($toaddr);
                }
            }
        } else {
            if (count($this->to) > 0) {
                if ('mail' !== $this->Mailer) {
                    $result .= $this->addrAppend('To', $this->to);
                }
            } elseif (count($this->cc) === 0) {
                $result .= $this->headerLine('To', 'undisclosed-recipients:;');
            }
        }
        $result .= $this->addrAppend('From', [[$this->From, $this->FromName]]);
        if (count($this->ReplyTo) > 0) {
            $result .= $this->addrAppend('Reply-To', $this->ReplyTo);
        }
        if ('mail' !== $this->Mailer) {
            if (count($this->cc) > 0) {
                $result .= $this->addrAppend('Cc', $this->cc);
            }
        }
        $result .= $this->headerLine('Subject', $this->encodeHeader($this->secureHeader($this->Subject)));
        if ('' !== $this->MessageID && preg_match('/^<.*@.*>$/', $this->MessageID)) {
            $this->lastMessageID = $this->MessageID;
        } else {
            $this->lastMessageID = sprintf('<%s@%s>', $this->uniqueid, $this->serverHostname());
        }
        $result .= $this->headerLine('Message-ID', $this->lastMessageID);
        if (null !== $this->Priority) {
            $result .= $this->headerLine('X-Priority', $this->Priority);
        }
        if ('' === $this->XMailer) {
            $result .= $this->headerLine('X-Mailer', 'PHPMailer ' . $this->Version . ' (https://github.com/PHPMailer/PHPMailer)');
        } elseif ($this->XMailer) {
            $result .= $this->headerLine('X-Mailer', trim($this->XMailer));
        }
        if ('' !== $this->ConfirmReadingTo) {
            $result .= $this->headerLine('Disposition-Notification-To', '<' . $this->ConfirmReadingTo . '>');
        }
        foreach ($this->CustomHeader as $header) {
            $result .= $this->headerLine(
                trim($header[0]),
                $this->encodeHeader(trim($header[1]))
            );
        }
        if (!$this->sign_key_file) {
            $result .= $this->headerLine('MIME-Version', '1.0');
            $result .= $this->getMailMIME();
        }
        return $result;
    }

    public function getMailMIME()
    {
        $result = '';
        $ismultipart = true;
        switch ($this->message_type) {
            case 'inline':
                $result .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_RELATED . ';');
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
            case 'attach':
            case 'inline_attach':
            case 'alt_attach':
            case 'alt_inline_attach':
                $result .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_MIXED . ';');
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
            case 'alt':
            case 'alt_inline':
                $result .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_ALTERNATIVE . ';');
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
            default:
                $ismultipart = false;
                $result .= $this->headerLine('Content-Type', $this->ContentType . '; charset=' . $this->CharSet);
                $result .= $this->headerLine('Content-Transfer-Encoding', $this->Encoding);
        }
        if ('mail' === $this->Mailer && $ismultipart) {
            $this->mailHeader .= $result;
            $result = '';
        }
        return $result;
    }

    public function getSentMIMEMessage()
    {
        return rtrim($this->MIMEHeader . $this->mailHeader, "\n\r") . static::$LE . static::$LE . $this->MIMEBody;
    }

    protected function generateId()
    {
        $len = 32;
        $bytes = '';
        if (function_exists('random_bytes')) {
            try {
                $bytes = random_bytes($len);
            } catch (\Exception $e) {
                // Do nothing — fall through to the alternative below
            }
        }
        if ($bytes === '' && function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($len);
        }
        if ($bytes === '') {
            $bytes = hash('sha256', uniqid((string) mt_rand(), true), true);
        }
        return str_replace(['/', '+', '='], '', base64_encode(hash('sha256', $bytes, true)));
    }

    protected function createBody()
    {
        $body = '';
        $this->boundary[1] = $this->generateId();
        $this->boundary[2] = $this->generateId();
        $this->boundary[3] = $this->generateId();
        if ($this->sign_key_file) {
            $body .= $this->getMailMIME() . static::$LE;
        }
        $this->setWordWrap();
        $bodyEncoding = $this->Encoding;
        $bodyCharSet = $this->CharSet;
        if (static::ENCODING_8BIT === $bodyEncoding && !$this->has8bitChars($this->Body)) {
            $bodyEncoding = static::ENCODING_7BIT;
            $bodyCharSet = static::CHARSET_ASCII;
        }
        $altBodyEncoding = $this->Encoding;
        $altBodyCharSet = $this->CharSet;
        if (static::ENCODING_8BIT === $altBodyEncoding && !$this->has8bitChars($this->AltBody)) {
            $altBodyEncoding = static::ENCODING_7BIT;
            $altBodyCharSet = static::CHARSET_ASCII;
        }
        $this->message_type = $this->setMessageType();
        switch ($this->message_type) {
            case 'alt':
                $body .= $this->getBoundary($this->boundary[1], $altBodyCharSet, static::CONTENT_TYPE_PLAINTEXT, $altBodyEncoding);
                $body .= $this->encodeString($this->AltBody, $altBodyEncoding);
                $body .= static::$LE;
                $body .= $this->getBoundary($this->boundary[1], $bodyCharSet, static::CONTENT_TYPE_TEXT_HTML, $bodyEncoding);
                $body .= $this->encodeString($this->Body, $bodyEncoding);
                $body .= static::$LE;
                if (!empty($this->Ical)) {
                    $body .= $this->getBoundary($this->boundary[1], '', static::CONTENT_TYPE_TEXT_CALENDAR . '; method=REQUEST', '');
                    $body .= $this->encodeString($this->Ical, $this->Encoding);
                    $body .= static::$LE;
                }
                $body .= $this->endBoundary($this->boundary[1]);
                break;
            case 'plain':
                $body .= $this->encodeString($this->Body, $bodyEncoding);
                break;
            default:
                $body .= $this->encodeString($this->Body, $bodyEncoding);
        }
        if ($this->isError()) {
            $body = '';
        } elseif ($this->sign_key_file) {
            try {
                if (!defined('PKCS7_TEXT')) {
                    throw new Exception($this->lang('extension_missing') . 'openssl');
                }
                $file = tempnam(sys_get_temp_dir(), 'srcsign');
                $signed = tempnam(sys_get_temp_dir(), 'mailsign');
                file_put_contents($file, $body);
                $privKeyStr = !empty($this->sign_key_pass) ?
                    openssl_pkey_get_private($this->sign_key_file, $this->sign_key_pass) :
                    openssl_pkey_get_private($this->sign_key_file);
                if (openssl_pkcs7_sign($file, $signed, 'file://' . realpath($this->sign_cert_file), $privKeyStr, [])) {
                    @unlink($file);
                    $body = file_get_contents($signed);
                    @unlink($signed);
                } else {
                    @unlink($file);
                    @unlink($signed);
                    throw new Exception($this->lang('signing') . openssl_error_string());
                }
            } catch (Exception $exc) {
                $body = '';
                if ($this->exceptions) {
                    throw $exc;
                }
            }
        }
        return $body;
    }

    protected function getBoundary($boundary, $charSet, $contentType, $encoding)
    {
        $result = '';
        if ('' === $charSet) {
            $charSet = $this->CharSet;
        }
        if ('' === $contentType) {
            $contentType = $this->ContentType;
        }
        if ('' === $encoding) {
            $encoding = $this->Encoding;
        }
        $result .= $this->textLine('--' . $boundary);
        $result .= sprintf('Content-Type: %s; charset=%s', $contentType, $charSet);
        $result .= static::$LE;
        if (static::ENCODING_7BIT !== $encoding && static::ENCODING_RAW !== $encoding) {
            $result .= $this->headerLine('Content-Transfer-Encoding', $encoding);
        }
        $result .= static::$LE;
        return $result;
    }

    protected function endBoundary($boundary)
    {
        return static::$LE . '--' . $boundary . '--' . static::$LE;
    }

    protected function setMessageType()
    {
        $type = [];
        if ($this->alternativeExists()) {
            $type[] = 'alt';
        }
        if (count($type) === 0) {
            $type[] = 'plain';
        }
        $this->message_type = implode('_', $type);
        if ('' === $this->message_type) {
            $this->message_type = 'plain';
        }
        return $this->message_type;
    }

    public function headerLine($name, $value)
    {
        return $name . ': ' . $value . static::$LE;
    }

    public function textLine($value)
    {
        return $value . static::$LE;
    }

    public function addAttachment($path, $name = '', $encoding = self::ENCODING_BASE64, $type = '', $disposition = 'attachment')
    {
        try {
            if (!static::fileIsAccessible($path)) {
                throw new Exception($this->lang('file_access') . $path, self::STOP_CONTINUE);
            }
            if ('' === $type) {
                $type = static::filenameToType($path);
            }
            $filename = (string) static::mb_pathinfo($path, PATHINFO_BASENAME);
            if ('' === $name) {
                $name = $filename;
            }
            if (!$this->validateEncoding($encoding)) {
                throw new Exception($this->lang('encoding') . $encoding, self::STOP_CONTINUE);
            }
            $this->attachment[] = [
                0 => $path,
                1 => $filename,
                2 => $name,
                3 => $encoding,
                4 => $type,
                5 => false,
                6 => $disposition,
                7 => 0,
            ];
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            $this->edebug($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }
            return false;
        }
        return true;
    }

    protected function addrAppend($type, $addr)
    {
        $addresses = [];
        foreach ($addr as $address) {
            $addresses[] = $this->addrFormat($address);
        }
        return $this->headerLine($type, implode(', ', $addresses));
    }

    public function addrFormat($addr)
    {
        if (empty($addr[1])) {
            return $this->secureHeader($addr[0]);
        }
        return $this->encodeHeader($this->secureHeader($addr[1]), 'phrase') . ' <' . $this->secureHeader($addr[0]) . '>';
    }

    public function wrapText($message, $length, $qp_mode = false)
    {
        if ($qp_mode) {
            $soft_break = sprintf(' =%s', static::$LE);
        } else {
            $soft_break = static::$LE;
        }
        if ($this->HasMultiBytes($message)) {
            return $this->wrapTextMB($message, $length, $soft_break, $qp_mode);
        }
        $charMax = 76;
        if ($qp_mode && $length > $charMax) {
            $length = $charMax;
        }
        $lines = explode(static::$LE, $message);
        $message = '';
        foreach ($lines as $line) {
            $words = explode(' ', $line);
            $buf = '';
            $firstWord = true;
            foreach ($words as $word) {
                if ($qp_mode && (strlen($word) > $length)) {
                    $space_left = $length - strlen($buf) - $qp_mode;
                    if (!$firstWord) {
                        if ($space_left > 20 || !$qp_mode) {
                            $len = $space_left;
                            if ($qp_mode) {
                                $len -= 1;
                            }
                            $word_part = substr($word, 0, $len);
                            $word = substr($word, $len);
                            $buf .= ' ' . $word_part;
                            $message .= $buf . sprintf('=%s', static::$LE);
                        } else {
                            $message .= $buf . $soft_break;
                        }
                        $buf = '';
                    }
                    while (strlen($word) > 0) {
                        if ($length <= 0) {
                            break;
                        }
                        $len = $length;
                        if ($qp_mode) {
                            $len -= 1;
                        }
                        $word_part = substr($word, 0, $len);
                        $word = substr($word, $len);
                        if (!empty($word)) {
                            $message .= $word_part . sprintf('=%s', static::$LE);
                        } else {
                            $buf = $word_part;
                        }
                    }
                } else {
                    $buf_o = $buf;
                    if (!$firstWord) {
                        $buf .= ' ';
                    }
                    $buf .= $word;
                    if (strlen($buf) > $length && $buf_o !== '') {
                        $message .= $buf_o . $soft_break;
                        $buf = $word;
                    }
                }
                $firstWord = false;
            }
            $message .= $buf . static::$LE;
        }
        return $message;
    }

    public function setWordWrap()
    {
        if ($this->WordWrap < 1) {
            return;
        }
        switch ($this->message_type) {
            case 'alt':
            case 'alt_inline':
            case 'alt_attach':
            case 'alt_inline_attach':
                $this->AltBody = $this->wrapText($this->AltBody, $this->WordWrap);
                break;
            default:
                $this->Body = $this->wrapText($this->Body, $this->WordWrap);
                break;
        }
    }

    public function encodeHeader($str, $position = 'text')
    {
        $matchcount = 0;
        switch (strtolower($position)) {
            case 'phrase':
                if (!preg_match('/[\200-\377]/', $str)) {
                    $encoded = addcslashes($str, "\0..\37\177\\\"");
                    if (($str === $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str)) {
                        return $encoded;
                    }
                    return "\"$encoded\"";
                }
                $matchcount = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
                break;
            case 'comment':
                $matchcount = preg_match_all('/[()"]/', $str, $matches);
                break;
            case 'text':
            default:
                $matchcount = preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
                break;
        }
        if ($this->has8bitChars($str)) {
            $charset = $this->CharSet;
        } else {
            $charset = static::CHARSET_ASCII;
        }
        $overhead = 75 - 7 - strlen($charset);
        if ('X-AnonMailer' === $position || 'none' === $position) {
            return $str;
        }
        if (static::CHARSET_ASCII === $charset || $matchcount < 1) {
            return $str;
        }
        $maxlen = 75 - 7 - strlen($charset);
        $encoded = $this->base64EncodeWrapMB($str, "\n");
        $encoded = trim(str_replace("\n", "\n =?$charset?B?", $encoded));
        $encoded = preg_replace('/^(.*)$/m', ' =?' . $charset . '?B?\\1?=', $encoded);
        return trim(str_replace(' =?' . $charset . '?B??=', '', $encoded));
    }

    public function base64EncodeWrapMB($str, $linebreak = null)
    {
        $start = '=?' . $this->CharSet . '?B?';
        $end = '?=';
        $encoded = '';
        if (null === $linebreak) {
            $linebreak = static::$LE;
        }
        $mb_length = mb_strlen($str, $this->CharSet);
        $length = 75 - strlen($start) - strlen($end);
        $ratio = $mb_length / strlen($str);
        $avgLength = floor($length * $ratio * .75);
        $offset = 0;
        for ($i = 0; $i < $mb_length; $i += $offset) {
            $lookBack = 0;
            do {
                $offset = $avgLength - $lookBack;
                $chunk = mb_substr($str, $i, $offset, $this->CharSet);
                $chunk = base64_encode($chunk);
                $lookBack++;
            } while (strlen($chunk) > $length);
            $encoded .= $chunk . $linebreak;
        }
        $encoded = substr($encoded, 0, -strlen($linebreak));
        return $encoded;
    }

    public function encodeQPphp($string)
    {
        return $this->encodeQP($string);
    }

    public function encodeQP($string)
    {
        return static::normalizeBreaks(quoted_printable_encode($string));
    }

    public function encodeQ($str, $position = 'text')
    {
        $pattern = '';
        $encoded = str_replace(["\r", "\n"], '', $str);
        switch (strtolower($position)) {
            case 'phrase':
                $pattern = '^A-Za-z0-9!*+\/ -';
                break;
            case 'comment':
                $pattern = '\(\)"';
                break;
            case 'text':
            default:
                $pattern = '\000-\011\013\014\016-\037\075\077\137\177-\377' . $pattern;
                break;
        }
        $matches = [];
        if (preg_match_all("/[{$pattern}]/", $encoded, $matches)) {
            $eqkey = array_search('=', $matches[0], true);
            if (false !== $eqkey) {
                unset($matches[0][$eqkey]);
                array_unshift($matches[0], '=');
            }
            foreach (array_unique($matches[0]) as $char) {
                $encoded = str_replace($char, '=' . sprintf('%02X', ord($char)), $encoded);
            }
        }
        return str_replace(' ', '_', $encoded);
    }

    public function encodeString($str, $encoding = self::ENCODING_BASE64)
    {
        $encoded = '';
        switch (strtolower($encoding)) {
            case static::ENCODING_BASE64:
                $encoded = chunk_split(
                    base64_encode($str),
                    static::STD_LINE_LENGTH,
                    static::$LE
                );
                break;
            case static::ENCODING_7BIT:
            case static::ENCODING_8BIT:
                $encoded = static::normalizeBreaks($str);
                if (strlen($encoded) > 0 && $encoded[strlen($encoded) - 1] !== static::$LE) {
                    $encoded .= static::$LE;
                }
                break;
            case static::ENCODING_QUOTED_PRINTABLE:
                $encoded = $this->encodeQP($str);
                break;
            default:
                $this->setError($this->lang('encoding') . $encoding);
                break;
        }
        return $encoded;
    }

    public function has8bitChars($text)
    {
        return (bool) preg_match('/[\x80-\xFF]/', $text);
    }

    public static function hasMultiBytes($str)
    {
        if (function_exists('mb_strlen')) {
            return strlen($str) > mb_strlen($str, '8bit');
        }
        return (1 < preg_match_all('/[\xc0-\xd6][\x80-\xbf]/', $str, $matches));
    }

    public static function isValidHost($host)
    {
        return !preg_match('/[\x00-\x1F]/', $host);
    }

    public function getError()
    {
        return $this->ErrorInfo;
    }

    public function isError()
    {
        return $this->error_count > 0;
    }

    protected function setError($msg)
    {
        $this->error_count++;
        if ($this->Mailer === 'smtp' && null !== $this->smtp) {
            $lasterror = $this->smtp->getError();
            if (!empty($lasterror['error'])) {
                $msg .= $this->lang('smtp_error') . $lasterror['error'];
                if (!empty($lasterror['detail'])) {
                    $msg .= ' Detail: ' . $lasterror['detail'];
                }
                if (!empty($lasterror['smtp_code'])) {
                    $msg .= ' SMTP code: ' . $lasterror['smtp_code'];
                }
                if (!empty($lasterror['smtp_code_ex'])) {
                    $msg .= ' Additional SMTP info: ' . $lasterror['smtp_code_ex'];
                }
            }
        }
        $this->ErrorInfo = $msg;
    }

    public function secureHeader($str)
    {
        return trim(str_replace(["\r", "\n"], '', $str));
    }

    public static function normalizeBreaks($text, $breaktype = null)
    {
        if (null === $breaktype) {
            $breaktype = static::$LE;
        }
        return preg_replace('/(\r\n|\r|\n)/m', $breaktype, $text);
    }

    protected function lang($key)
    {
        $PHPMAILER_LANG = [
            'authenticate'      => 'SMTP Error: Could not authenticate.',
            'connect_host'      => 'SMTP Error: Could not connect to SMTP host.',
            'data_not_accepted' => 'SMTP Error: data not accepted.',
            'empty_message'     => 'Message body empty.',
            'encoding'          => 'Unknown encoding: ',
            'execute'           => 'Could not execute: ',
            'file_access'       => 'Could not access file: ',
            'file_open'         => 'File Error: Could not open file: ',
            'from_failed'       => 'The following From address failed: ',
            'instantiate'       => 'Could not instantiate mail function.',
            'invalid_address'   => 'Invalid address',
            'mailer_not_supported' => ' mailer is not supported.',
            'provide_address'   => 'You must provide at least one recipient email address.',
            'recipients_failed' => 'SMTP Error: The following recipients failed: ',
            'signing'           => 'Signing Error: ',
            'smtp_connect_failed' => 'SMTP connect() failed.',
            'smtp_error'        => 'SMTP server error: ',
            'variable_set'      => 'Cannot set or reset variable: ',
            'extension_missing' => 'Extension missing: ',
        ];
        if (array_key_exists($key, $PHPMAILER_LANG)) {
            return $PHPMAILER_LANG[$key];
        }
        return 'Language string failed to load: ' . $key;
    }

    public function alternativeExists()
    {
        return !empty($this->AltBody);
    }

    protected function doCallback($isSent, $to, $cc, $bcc, $subject, $body, $from, $extra)
    {
        if (!empty($this->action_function) && is_callable($this->action_function)) {
            call_user_func($this->action_function, $isSent, $to, $cc, $bcc, $subject, $body, $from, $extra);
        }
    }

    public static function validateAddress($address, $patternselect = null)
    {
        if (null === $patternselect) {
            $patternselect = static::$validator;
        }
        if (is_callable($patternselect)) {
            return call_user_func($patternselect, $address);
        }
        if (strpos($address, "\n") !== false || strpos($address, "\r") !== false) {
            return false;
        }
        switch ($patternselect) {
            case 'pcre8':
                return (bool) preg_match(
                    '/^(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){255,})(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){65,}@)((?>(?>(?>((?>(?>(?>\x0D\x0A)?[\t ])+|(?>[\t ]*\x0D\x0A)?[\t ]+)?)(\((?>(?2)(?>[\x01-\x08\x0B\x0C\x0E-\'*-\[\]-\x7F]|\\\[\x00-\x7F]|(?3)))*(?2)\)))+(?2))|(?2))?)([!#-\'*+\/-9=?^-~-]+|"(?2)(?>(?>\\\[ -~]|[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F])(?2))*"(?2))(?>\.(?2)(?>[!#-\'*+\/-9=?^-~-]+|"(?2)(?>(?>\\\[ -~]|[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F])(?2))*"(?2)))*(?2)@(?2)(?>(?!(?2a))(?>(?>[a-zA-Z0-9](?>[a-zA-Z0-9-]*[a-zA-Z0-9])?)(?>\.(?!(?2a))(?>[a-zA-Z0-9](?>[a-zA-Z0-9-]*[a-zA-Z0-9])?))*)(?>\.(?>[a-zA-Z]{2,})))$/isD',
                    $address
                );
            case 'html5':
                return (bool) preg_match(
                    '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/sD',
                    $address
                );
            case 'php':
            default:
                return (bool) filter_var($address, FILTER_VALIDATE_EMAIL);
        }
    }

    public static function rfcDate()
    {
        $tz  = date('Z');
        $tzs = ($tz < 0 ? '-' : '+');
        $tz  = abs($tz);
        $tz  = (int) ($tz / 3600) * 100 + ($tz % 3600) / 60;
        return sprintf('%s %s%04d', date('D, j M Y H:i:s'), $tzs, $tz);
    }

    protected function serverHostname()
    {
        $result = '';
        if (!empty($this->Hostname)) {
            $result = $this->Hostname;
        } elseif (isset($_SERVER) && array_key_exists('SERVER_NAME', $_SERVER)) {
            $result = $_SERVER['SERVER_NAME'];
        } elseif (function_exists('gethostname') && gethostname() !== false) {
            $result = gethostname();
        } elseif (php_uname('n') !== false) {
            $result = php_uname('n');
        }
        if (!static::isValidHost($result)) {
            return 'localhost.localdomain';
        }
        return $result;
    }

    public static function filenameToType($filename)
    {
        $mimetype = 'application/octet-stream';
        $ext = static::mb_pathinfo($filename, PATHINFO_EXTENSION);
        $mimes = [
            'xl'    => 'application/excel',
            'js'    => 'application/javascript',
            'hqx'   => 'application/mac-binhex40',
            'cpt'   => 'application/mac-compactpro',
            'bin'   => 'application/macbinary',
            'doc'   => 'application/msword',
            'word'  => 'application/msword',
            'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xltx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'potx'  => 'application/vnd.openxmlformats-officedocument.presentationml.template',
            'ppsx'  => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'pptx'  => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'sldx'  => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dotx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'xlam'  => 'application/vnd.ms-excel.addin.macroEnabled.12',
            'xlsb'  => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'class' => 'application/octet-stream',
            'dll'   => 'application/octet-stream',
            'dms'   => 'application/octet-stream',
            'exe'   => 'application/octet-stream',
            'lha'   => 'application/octet-stream',
            'lzh'   => 'application/octet-stream',
            'psd'   => 'application/octet-stream',
            'sea'   => 'application/octet-stream',
            'so'    => 'application/octet-stream',
            'pdf'   => 'application/pdf',
            'ai'    => 'application/postscript',
            'eps'   => 'application/postscript',
            'ps'    => 'application/postscript',
            'smi'   => 'application/smil',
            'smil'  => 'application/smil',
            'mif'   => 'application/vnd.mif',
            'xls'   => 'application/vnd.ms-excel',
            'ppt'   => 'application/vnd.ms-powerpoint',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wmlc'  => 'application/vnd.wap.wmlc',
            'dcr'   => 'application/x-director',
            'dir'   => 'application/x-director',
            'dxr'   => 'application/x-director',
            'dvi'   => 'application/x-dvi',
            'gtar'  => 'application/x-gtar',
            'php'   => 'application/x-httpd-php',
            'php4'  => 'application/x-httpd-php',
            'php3'  => 'application/x-httpd-php',
            'phtml' => 'application/x-httpd-php',
            'phps'  => 'application/x-httpd-php-source',
            'swf'   => 'application/x-shockwave-flash',
            'sit'   => 'application/x-stuffit',
            'tar'   => 'application/x-tar',
            'tgz'   => 'application/x-tar',
            'z'     => 'application/x-compress',
            'gz'    => 'application/x-gzip',
            'zip'   => 'application/zip',
            'midi'  => 'audio/midi',
            'mid'   => 'audio/midi',
            'mp2'   => 'audio/mpeg',
            'mp3'   => 'audio/mpeg',
            'mpga'  => 'audio/mpeg',
            'aif'   => 'audio/x-aiff',
            'aifc'  => 'audio/x-aiff',
            'aiff'  => 'audio/x-aiff',
            'ram'   => 'audio/x-pn-realaudio',
            'rm'    => 'audio/x-pn-realaudio',
            'rpm'   => 'audio/x-pn-realaudio-plugin',
            'ra'    => 'audio/x-realaudio',
            'rv'    => 'video/vnd.rn-realvideo',
            'wav'   => 'audio/x-wav',
            'bmp'   => 'image/bmp',
            'gif'   => 'image/gif',
            'jpeg'  => 'image/jpeg',
            'jpe'   => 'image/jpeg',
            'jpg'   => 'image/jpeg',
            'png'   => 'image/png',
            'tiff'  => 'image/tiff',
            'tif'   => 'image/tiff',
            'webp'  => 'image/webp',
            'svg'   => 'image/svg+xml',
            'eml'   => 'message/rfc822',
            'css'   => 'text/css',
            'html'  => 'text/html',
            'htm'   => 'text/html',
            'shtml' => 'text/html',
            'log'   => 'text/plain',
            'text'  => 'text/plain',
            'txt'   => 'text/plain',
            'rtx'   => 'text/richtext',
            'rtf'   => 'text/rtf',
            'vcf'   => 'text/vcard',
            'vcard' => 'text/vcard',
            'ics'   => 'text/calendar',
            'xml'   => 'text/xml',
            'xsl'   => 'text/xml',
            'mpeg'  => 'video/mpeg',
            'mpe'   => 'video/mpeg',
            'mpg'   => 'video/mpeg',
            'mov'   => 'video/quicktime',
            'qt'    => 'video/quicktime',
            'rv'    => 'video/vnd.rn-realvideo',
            'avi'   => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie',
            'mp4'   => 'video/mp4',
        ];
        if (array_key_exists(strtolower($ext), $mimes)) {
            return $mimes[strtolower($ext)];
        }
        return $mimetype;
    }

    public static function mb_pathinfo($path, $options = null)
    {
        $ret = ['dirname' => '', 'basename' => '', 'extension' => '', 'filename' => ''];
        $pathinfo = [];
        if (preg_match('#^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^.\\\\/]+?)|))[\\\\/.]*$#m', $path, $pathinfo)) {
            if (array_key_exists(1, $pathinfo)) {
                $ret['dirname'] = $pathinfo[1];
            }
            if (array_key_exists(2, $pathinfo)) {
                $ret['basename'] = $pathinfo[2];
            }
            if (array_key_exists(5, $pathinfo)) {
                $ret['extension'] = $pathinfo[5];
            }
            if (array_key_exists(3, $pathinfo)) {
                $ret['filename'] = $pathinfo[3];
            }
        }
        switch ($options) {
            case PATHINFO_DIRNAME:
            case 'dirname':
                return $ret['dirname'];
            case PATHINFO_BASENAME:
            case 'basename':
                return $ret['basename'];
            case PATHINFO_EXTENSION:
            case 'extension':
                return $ret['extension'];
            case PATHINFO_FILENAME:
            case 'filename':
                return $ret['filename'];
            default:
                return $ret;
        }
    }

    public static function fileIsAccessible($path)
    {
        if (!static::isPermittedPath($path)) {
            return false;
        }
        $readable = file_exists($path);
        return $readable;
    }

    public static function isPermittedPath($path)
    {
        if (!$path || (strpos($path, '..') !== false)) {
            return false;
        }
        return true;
    }

    // Constants needed for stop levels
    const STOP_MESSAGE  = 0;
    const STOP_CONTINUE = 1;
    const STOP_CRITICAL = 2;

    // Line ending
    public static $LE = "\r\n";

    // Validator
    public static $validator = 'php';

    // Standard line length for SMTP
    const STD_LINE_LENGTH = 76;

    // Text/plain content type fix
    const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';
}
