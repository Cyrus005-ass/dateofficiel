<?php
/**
 * SHOPPING DATE — mail.php
 * Envoi email 100% PHP natif — compatible PHP 7.0+
 * SMTP maison (sockets) + fallback mail()
 * Config : includes/smtp_config.php
 */

require_once __DIR__ . '/db.php';

/* ══════════════════════════════════════════════════════
   sendSelectionEmail — point d'entrée principal
   ══════════════════════════════════════════════════════ */
function sendSelectionEmail($p)
{
    $cfgFile = __DIR__ . '/smtp_config.php';
    $cfg = file_exists($cfgFile) ? require $cfgFile : array();

    $prenom = htmlspecialchars($p['prenom']);
    $nom    = htmlspecialchars($p['nom']);
    $html   = buildSelectionEmailHtml($prenom, $nom);
    $text   = buildSelectionEmailText($prenom, $nom);

    // SMTP configuré et non vide ?
    $smtpOk = !empty($cfg['host'])
           && !empty($cfg['username'])
           && $cfg['username'] !== 'votre@gmail.com'
           && !empty($cfg['password']);

    if ($smtpOk) {
        $res = smtpSend(
            $cfg,
            $p['email'],
            $prenom . ' ' . $nom,
            '=?UTF-8?B?' . base64_encode('Shopping Date - Felicitations ' . $prenom . ' !') . '?=',
            $html,
            $text
        );
        if ($res === true) {
            markEmailSent((int)$p['id']);
            return array('ok' => true, 'msg' => 'Email envoye a ' . $p['email']);
        }
        // SMTP a echoue, on tente mail()
    }

    // Fallback mail() natif
    if (nativeMail($p['email'], $prenom . ' ' . $nom, $html)) {
        markEmailSent((int)$p['id']);
        return array('ok' => true, 'msg' => 'Email envoye (mail natif)');
    }

    $errDetail = isset($res) && $res !== true ? ' (' . $res . ')' : '';
    return array('ok' => false, 'msg' => 'Email non envoye' . $errDetail . '. Configurez smtp_config.php');
}

/* ══════════════════════════════════════════════════════
   smtpSend — SMTP natif via sockets PHP
   Retourne true ou un message d'erreur string
   ══════════════════════════════════════════════════════ */
function smtpSend($cfg, $toEmail, $toName, $subject, $html, $text)
{
    $host = $cfg['host'];
    $port = isset($cfg['port']) ? (int)$cfg['port'] : 587;
    $user = $cfg['username'];
    $pass = $cfg['password'];
    $from = isset($cfg['from_email']) ? $cfg['from_email'] : $user;
    $fromName = isset($cfg['from_name']) ? $cfg['from_name'] : 'Shopping Date';
    $enc  = isset($cfg['encryption']) ? strtolower($cfg['encryption']) : 'tls';
    $timeout = isset($cfg['timeout']) ? (int)$cfg['timeout'] : 15;

    // Connexion socket
    $ctx = stream_context_create(array(
        'ssl' => array(
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        )
    ));

    $prefix = ($enc === 'ssl') ? 'ssl://' : '';
    $sock = @stream_socket_client(
        $prefix . $host . ':' . $port,
        $errno, $errstr, $timeout,
        STREAM_CLIENT_CONNECT,
        $ctx
    );

    if (!$sock) {
        return 'Connexion SMTP impossible : ' . $errstr . ' (' . $errno . ')';
    }

    stream_set_timeout($sock, $timeout);

    // Lire banniere
    $r = smtpRead($sock);
    if (substr($r, 0, 3) !== '220') { fclose($sock); return 'Banniere SMTP: ' . trim($r); }

    // EHLO
    smtpWrite($sock, 'EHLO ' . (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost'));
    $r = smtpRead($sock);

    // STARTTLS si demande
    if ($enc === 'tls') {
        smtpWrite($sock, 'STARTTLS');
        $r = smtpRead($sock);
        if (substr($r, 0, 3) !== '220') { fclose($sock); return 'STARTTLS refuse: ' . trim($r); }
        stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        smtpWrite($sock, 'EHLO ' . (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost'));
        smtpRead($sock);
    }

    // AUTH LOGIN
    smtpWrite($sock, 'AUTH LOGIN');
    $r = smtpRead($sock);
    if (substr($r, 0, 3) !== '334') { fclose($sock); return 'AUTH LOGIN refuse: ' . trim($r); }

    smtpWrite($sock, base64_encode($user));
    $r = smtpRead($sock);
    if (substr($r, 0, 3) !== '334') { fclose($sock); return 'Username refuse'; }

    smtpWrite($sock, base64_encode($pass));
    $r = smtpRead($sock);
    if (substr($r, 0, 3) !== '235') { fclose($sock); return 'Authentification echouee: ' . trim($r); }

    // MAIL FROM
    smtpWrite($sock, 'MAIL FROM:<' . $from . '>');
    $r = smtpRead($sock);
    if (substr($r, 0, 3) !== '250') { fclose($sock); return 'MAIL FROM refuse: ' . trim($r); }

    // RCPT TO
    smtpWrite($sock, 'RCPT TO:<' . $toEmail . '>');
    $r = smtpRead($sock);
    if (substr($r, 0, 3) !== '250' && substr($r, 0, 3) !== '251') {
        fclose($sock);
        return 'RCPT TO refuse: ' . trim($r);
    }

    // DATA
    smtpWrite($sock, 'DATA');
    $r = smtpRead($sock);
    if (substr($r, 0, 3) !== '354') { fclose($sock); return 'DATA refuse: ' . trim($r); }

    // Construire le message MIME multipart
    $boundary = 'SD_' . md5(uniqid());
    $msg  = 'Date: ' . date('r') . "\r\n";
    $msg .= 'From: =?UTF-8?B?' . base64_encode($fromName) . '?= <' . $from . ">\r\n";
    $msg .= 'To: =?UTF-8?B?' . base64_encode($toName) . '?= <' . $toEmail . ">\r\n";
    $msg .= 'Subject: ' . $subject . "\r\n";
    $msg .= 'MIME-Version: 1.0' . "\r\n";
    $msg .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"' . "\r\n";
    $msg .= 'X-Mailer: ShoppingDate-Mailer/1.0' . "\r\n";
    $msg .= "\r\n";
    // Partie texte
    $msg .= '--' . $boundary . "\r\n";
    $msg .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
    $msg .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
    $msg .= chunk_split(base64_encode($text)) . "\r\n";
    // Partie HTML
    $msg .= '--' . $boundary . "\r\n";
    $msg .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $msg .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
    $msg .= chunk_split(base64_encode($html)) . "\r\n";
    $msg .= '--' . $boundary . "--\r\n";
    $msg .= '.';

    smtpWrite($sock, $msg);
    $r = smtpRead($sock);
    $ok = (substr($r, 0, 3) === '250');

    smtpWrite($sock, 'QUIT');
    fclose($sock);

    return $ok ? true : 'Envoi refuse: ' . trim($r);
}

function smtpWrite($sock, $data)
{
    fwrite($sock, $data . "\r\n");
}

function smtpRead($sock)
{
    $out = '';
    while (!feof($sock)) {
        $line = fgets($sock, 512);
        $out .= $line;
        // Le dernier chunk n'a pas de tiret apres le code
        if (isset($line[3]) && $line[3] === ' ') break;
        if (strlen($line) < 4) break;
    }
    return $out;
}

/* ══════════════════════════════════════════════════════
   nativeMail — fallback php mail()
   ══════════════════════════════════════════════════════ */
function nativeMail($toEmail, $toName, $html)
{
    $boundary = 'SD_' . md5(uniqid());
    $subject  = '=?UTF-8?B?' . base64_encode('Shopping Date - Felicitations ! Vous etes selectionne(e)') . '?=';
    $headers  = 'From: Shopping Date <noreply@shopping-date.unaux.com>' . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'X-Mailer: ShoppingDate/1.0' . "\r\n";
    return @mail($toEmail, $subject, $html, $headers);
}

/* ══════════════════════════════════════════════════════
   markEmailSent
   ══════════════════════════════════════════════════════ */
function markEmailSent($id)
{
    getPDO()
        ->prepare("UPDATE selections SET notification_sent=1, date_notification=NOW() WHERE participant_id=?")
        ->execute(array($id));
}

/* ══════════════════════════════════════════════════════
   Corps HTML de l'email
   ══════════════════════════════════════════════════════ */
function buildSelectionEmailHtml($prenom, $nom)
{
    $year = date('Y');
    return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#070707;font-family:Arial,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#070707;padding:30px 10px">
<tr><td align="center">
<table width="100%" style="max-width:560px" cellpadding="0" cellspacing="0">
<tr><td style="background:#FF1E9E;border-radius:14px 14px 0 0;padding:32px;text-align:center">
  <h1 style="margin:0;color:#fff;font-size:26px;letter-spacing:5px;font-family:Georgia,serif">SHOPPING DATE</h1>
  <p style="margin:8px 0 0;color:rgba(255,255,255,.85);font-size:11px;letter-spacing:3px">par Muse Origin Studio</p>
</td></tr>
<tr><td style="background:#111;border:1px solid #222;border-top:none;padding:32px">
  <p style="text-align:center;font-size:44px;margin:0 0 18px">&#127881;</p>
  <h2 style="margin:0 0 14px;color:#fff;font-size:19px">Felicitations, ' . $prenom . ' ' . $nom . ' !</h2>
  <p style="margin:0 0 14px;color:#999;font-size:14px;line-height:1.8">
    Votre profil a ete <strong style="color:#FF1E9E">selectionne(e)</strong> pour participer a
    <strong style="color:#fff">Shopping Date</strong> Saison 1 !
  </p>
  <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0">
  <tr><td style="background:#1a0a10;border:1px solid #FF1E9E;border-radius:10px;padding:20px;text-align:center">
    <p style="margin:0 0 5px;font-size:32px;font-weight:900;color:#FF1E9E">10 000 FCFA</p>
    <p style="margin:0;color:#666;font-size:11px;letter-spacing:2px;text-transform:uppercase">Budget shopping</p>
  </td></tr></table>
  <p style="margin:0 0 10px;color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px">Les etapes :</p>
  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px"><tr>
    <td width="32" valign="top"><div style="width:24px;height:24px;background:#FF1E9E;border-radius:50%;text-align:center;line-height:24px;color:#fff;font-size:11px;font-weight:700">1</div></td>
    <td style="padding-left:10px;color:#999;font-size:13px;line-height:1.7">Recevez <strong style="color:#FF1E9E">10 000 FCFA</strong> pour preparer votre look.</td>
  </tr></table>
  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px"><tr>
    <td width="32" valign="top"><div style="width:24px;height:24px;background:#c040d0;border-radius:50%;text-align:center;line-height:24px;color:#fff;font-size:11px;font-weight:700">2</div></td>
    <td style="padding-left:10px;color:#999;font-size:13px;line-height:1.7">Shopping <strong style="color:#fff">separe</strong>, sans connaitre votre partenaire.</td>
  </tr></table>
  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:22px"><tr>
    <td width="32" valign="top"><div style="width:24px;height:24px;background:#cc0010;border-radius:50%;text-align:center;line-height:24px;color:#fff;font-size:11px;font-weight:700">3</div></td>
    <td style="padding-left:10px;color:#999;font-size:13px;line-height:1.7">Diner romantique filme - premiere rencontre !</td>
  </tr></table>
  <p style="margin:0 0 22px;color:#888;font-size:13px;line-height:1.8">Notre equipe vous contactera tres prochainement avec tous les details.</p>
  <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;padding-top:20px;border-top:1px solid #1f1f1f">
  <tr><td>
    <p style="margin:0 0 3px;color:#FF1E9E;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase">L equipe Shopping Date</p>
    <p style="margin:0;color:#333;font-size:10px">Muse Origin Studio &middot; contact@museoriginstudio.com</p>
  </td></tr></table>
</td></tr>
<tr><td style="background:#070707;border:1px solid #111;border-top:none;border-radius:0 0 14px 14px;padding:16px;text-align:center">
  <p style="margin:0;color:#1f1f1f;font-size:10px">&copy; ' . $year . ' Shopping Date &mdash; Muse Origin Studio.</p>
</td></tr>
</table>
</td></tr></table>
</body></html>';
}

function buildSelectionEmailText($prenom, $nom)
{
    return "SHOPPING DATE - par Muse Origin Studio\n"
         . "========================================\n\n"
         . "Felicitations, " . $prenom . " " . $nom . " !\n\n"
         . "Votre profil a ete SELECTIONNE pour Shopping Date Saison 1.\n\n"
         . "BUDGET : 10 000 FCFA\n\n"
         . "1. Recevez votre budget et preparez votre look.\n"
         . "2. Shopping separe, sans connaitre votre partenaire.\n"
         . "3. Diner romantique filme - premiere rencontre !\n\n"
         . "Notre equipe vous contactera prochainement.\n\n"
         . "L'equipe Shopping Date\n"
         . "contact@museoriginstudio.com\n"
         . "(c) " . date('Y') . " Shopping Date";
}
