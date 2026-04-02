<?php
/**
 * Copiez vers smtp_config.php et renseignez Mailtrap (local) / Gmail app password (prod).
 *   copy includes\smtp_config.example.php includes\smtp_config.php
 */
$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
$isLocal = in_array($host, ['localhost', '127.0.0.1', '::1'])
        || str_ends_with($host, '.local')
        || str_starts_with($host, '192.168.')
        || str_starts_with($host, '10.');

if ($isLocal) {
    return [
        'host'       => 'sandbox.smtp.mailtrap.io',
        'port'       => 2525,
        'encryption' => 'tls',
        'auth'       => true,
        'username'   => 'VOTRE_USERNAME_MAILTRAP',
        'password'   => 'VOTRE_PASSWORD_MAILTRAP',
        'from_email' => 'noreply@example.test',
        'from_name'  => 'Muse Origin Studio',
        'reply_to'   => 'noreply@example.test',
        'timeout'       => 30,
        'debug'         => 2,
        'fallback_mail' => false,
    ];
}

return [
    'host'       => 'smtp.gmail.com',
    'port'       => 587,
    'encryption' => 'tls',
    'auth'       => true,
    'username'   => 'votre-compte@gmail.com',
    'password'   => 'CHANGEME_MOT_DE_PASSE_APPLICATION_16_CARACTERES',
    'from_email' => 'votre-compte@gmail.com',
    'from_name'  => 'Muse Origin Studio',
    'reply_to'   => 'votre-compte@gmail.com',
    'timeout'       => 30,
    'debug'         => 0,
    'fallback_mail' => false,
];
