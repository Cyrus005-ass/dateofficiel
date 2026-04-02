<?php
/**
 * includes/config.example.php — modèle pour config.php
 * ══════════════════════════════════════════════════════════════════
 * 1. Copiez ce fichier :  includes/config.example.php  →  includes/config.php
 * 2. Renseignez les identifiants base de données PRODUCTION (hébergeur).
 * ══════════════════════════════════════════════════════════════════
 */

if (!isset($BASE)) {
    $docRoot    = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $projectDir = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');

    if ($docRoot !== '' && strpos($projectDir, $docRoot) === 0) {
        $BASE = substr($projectDir, strlen($docRoot));
        if ($BASE !== '' && $BASE[0] !== '/') $BASE = '/' . $BASE;
    } else {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $dir    = dirname($script);
        $parts  = array_filter(explode('/', $dir));
        $base   = '';
        for ($i = count($parts); $i >= 0; $i--) {
            $webPath = $i > 0 ? '/' . implode('/', array_slice($parts, 0, $i)) : '';
            $fsPath  = $docRoot . $webPath;
            if (is_dir($fsPath . '/includes') && is_dir($fsPath . '/assets')) {
                $base = $webPath;
                break;
            }
        }
        $BASE = $base;
    }
}

if (!defined('IS_LOCAL')) {
    $__h = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? 'localhost')[0]);
    $__local = in_array($__h, ['localhost', '127.0.0.1', '::1'])
            || substr($__h, -6) === '.local'
            || preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2\d|3[01])\.)/', $__h);
    define('IS_LOCAL', $__local);
    define('ENV', $__local ? 'local' : 'production');
}

if (!defined('DB_HOST')) {
    if (IS_LOCAL) {
        define('DB_HOST', '127.0.0.1');
        define('DB_NAME', 'shopping-date');
        define('DB_USER', 'root');
        define('DB_PASS', '');
    } else {
        define('DB_HOST', 'CHANGEME_DB_HOST');
        define('DB_NAME', 'CHANGEME_DB_NAME');
        define('DB_USER', 'CHANGEME_DB_USER');
        define('DB_PASS', 'CHANGEME_DB_PASSWORD');
    }
}

$SITE_LINKS = $SITE_LINKS ?? [
    'instagram' => 'https://www.instagram.com/shoppingdateoff?igsh=MWNuY2JmdGg3MjhwYQ==&utm_s',
    'tiktok' => 'https://www.tiktok.com/@shoppingdateoff0?_r=1&_t=ZS-946UkzSxfqG',
    'facebook' => 'https://www.facebook.com/share/1JKCwwQcFV/?mibextid=wwXIfr',
    'youtube' => 'https://www.youtube.com/@lebenentertaimentChannel',
    'youtube_subscribe' => 'https://www.youtube.com/@lebenentertaimentChannel?sub_confirmation=1',
    'episode0' => 'https://youtu.be/c4IUUvX_q4Y?si=wqvaO9h1pJo7DCk7',
    'season1' => '#',
    'contact_email' => 'museorigin.studio@outlook.com',
];

$SITE_PARTNERS = $SITE_PARTNERS ?? [
    [
        'id' => 'producer',
        'name' => 'Muse Origin Studio',
        'role' => 'Producteur',
        'tagline' => 'Muse Origin Studio est une maison de production audiovisuelle dediee a la creation de contenus originaux, innovants et porteurs d emotions.',
        'description' => 'Notre vision : "where creativity meets professional production" — chaque projet pense avec passion et execute avec excellence. Shopping Date est notre derniere creation — un format inedit qui celebre l authenticite, le style et la magie de la premiere rencontre.',
        'logo' => $BASE . '/assets/img/muse.png',
        'website' => '#',
        'socials' => [
            ['label' => 'Facebook', 'url' => 'https://www.facebook.com/share/1FLW3crHK5/'],
            ['label' => 'WhatsApp', 'url' => 'https://wa.me/2290129327438'],
        ],
    ],
    [
        'id' => 'coproducer',
        'name' => 'Elite Event Agency',
        'role' => 'Co-producteur',
        'tagline' => 'Muse Origin Studio et Elite Event Agency',
        'logo' => $BASE . '/assets/img/eea.png',
        'website' => '#',
        'socials' => [
            ['label' => 'Instagram', 'url' => 'https://www.instagram.com/reel/DQH3gioDDpw/?igsh=MXE5dW9peG0xbXBjcg=='],
            ['label' => 'TikTok', 'url' => 'https://www.tiktok.com/@tomiton.min.si.no?_t=ZM-90loM27nVtL&_r=1'],
            ['label' => 'Facebook', 'url' => 'https://www.facebook.com/profile.php?id=61581084481131'],
        ],
    ],
    [
        'id' => 'broadcaster',
        'name' => 'Leben Entertainment',
        'role' => 'Diffuseur',
        'tagline' => 'Plateforme de diffusion generale du projet',
        'logo' => $BASE . '/assets/img/leben.png',
        'website' => 'https://www.youtube.com/@lebenentertaimentChannel',
        'socials' => [
            ['label' => 'YouTube', 'url' => 'https://www.youtube.com/@lebenentertaimentChannel'],
        ],
    ],
];

$TECH_TEAM = $TECH_TEAM ?? [
    ['name' => 'ATAYI Eliel', 'role' => 'Dev full & back-end', 'linkedin' => '#'],
    ['name' => "d'ALMEIDA Oswald", 'role' => 'Dev front-end', 'linkedin' => '#'],
    ['name' => 'Cyrus-y', 'role' => 'Dev front & back-end', 'linkedin' => 'https://www.linkedin.com/in/cyrus-youp-assogba-67a116329'],
    ['name' => 'Gisèle MAHINOU', 'role' => 'Support projet', 'linkedin' => '#'],
];

$PROJECT_DIRECTION = $PROJECT_DIRECTION ?? [
    ['name' => 'ASSOGBA Sewu', 'role' => 'Realisateur'],
    ['name' => 'Oriane CODJOVI', 'role' => 'Productrice'],
    ['name' => 'BOCOSSA Daniel', 'role' => 'Coproducteur'],
];
