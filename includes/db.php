<?php
// includes/db.php — Connexion PDO
// La config DB est dans config.php (chargé avant ce fichier)
if (!defined('DB_HOST')) require_once __DIR__ . '/config.php';

function getPDO() {
    static $pdo;
    if (!$pdo) {
        try {
            $pdo = new PDO(
                'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (\PDOException $e) {
            $envLabel = (ENV === 'local') ? 'local (WAMP/XAMPP/Laragon) — BDD: ' . DB_NAME : DB_HOST;
            http_response_code(503);
            die('<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Erreur DB</title>
<style>*{box-sizing:border-box;margin:0;padding:0}body{background:#070707;color:#fff;font-family:"DM Sans",sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}.box{background:#111;border:1px solid #FF1E9E;border-radius:20px;padding:40px;max-width:480px;text-align:center}h2{color:#FF1E9E;font-size:1.3rem;margin-bottom:12px}p{color:#888;font-size:.9rem;line-height:1.7}code{background:#1c1c1c;padding:3px 8px;border-radius:6px;color:#F057FF}</style>
</head><body><div class="box"><h2>&#9888;&#65039; Connexion base de données impossible</h2><p>Impossible de se connecter à <code>'.$envLabel.'</code>.<br>Vérifiez que MySQL est démarré et que la BDD existe.</p></div></body></html>');
        }
        ensureSchema($pdo);
    }
    return $pdo;
}

function ensureSchema($pdo) {
    static $done = false;
    if ($done) return;
    $done = true;
    $checkColumn = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'participants'
          AND COLUMN_NAME = ?
        LIMIT 1
    ");
    $required = [
        'partner_criteria' => "ALTER TABLE participants ADD COLUMN partner_criteria TEXT DEFAULT NULL AFTER profession",
        'red_flags' => "ALTER TABLE participants ADD COLUMN red_flags TEXT DEFAULT NULL AFTER partner_criteria",
        'green_flags' => "ALTER TABLE participants ADD COLUMN green_flags TEXT DEFAULT NULL AFTER red_flags",
        'ideal_date' => "ALTER TABLE participants ADD COLUMN ideal_date TEXT DEFAULT NULL AFTER green_flags",
        'carte_identite' => "ALTER TABLE participants ADD COLUMN carte_identite VARCHAR(255) DEFAULT NULL AFTER photo",
    ];
    foreach ($required as $column => $sql) {
        $checkColumn->execute([$column]);
        if (!$checkColumn->fetchColumn()) {
            $pdo->exec($sql);
        }
    }
}
