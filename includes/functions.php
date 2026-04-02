<?php
// includes/functions.php — Compatible PHP 7.0+
require_once __DIR__ . '/db.php';

function sanitize($v) {
    return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
}
function redirect($url) {
    header('Location: ' . $url); exit;
}
function requireAdmin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_id'])) {
        require_once __DIR__ . '/config.php';
        redirect($GLOBALS['BASE'] . '/admin/login.php');
    }
}
function initials($prenom, $nom) {
    return strtoupper(mb_substr($prenom,0,1) . mb_substr($nom,0,1));
}

/* ── Participants ── */
function getParticipants($statut = '') {
    $pdo = getPDO();
    if ($statut) {
        $s = $pdo->prepare("SELECT * FROM participants WHERE statut=? ORDER BY date_inscription DESC");
        $s->execute([$statut]);
    } else {
        $s = $pdo->query("SELECT * FROM participants ORDER BY date_inscription DESC");
    }
    return $s->fetchAll();
}
function getParticipant($id) {
    $s = getPDO()->prepare("SELECT * FROM participants WHERE id=?");
    $s->execute([$id]);
    $r = $s->fetch();
    return $r ? $r : null;
}
function countStats() {
    $pdo = getPDO();
    return [
        'total'   => (int)$pdo->query("SELECT COUNT(*) FROM participants")->fetchColumn(),
        'attente' => (int)$pdo->query("SELECT COUNT(*) FROM participants WHERE statut='en_attente'")->fetchColumn(),
        'sel'     => (int)$pdo->query("SELECT COUNT(*) FROM participants WHERE statut='selectionne'")->fetchColumn(),
        'rejete'  => (int)$pdo->query("SELECT COUNT(*) FROM participants WHERE statut='rejete'")->fetchColumn(),
    ];
}
function deleteParticipant($id) {
    $p = getParticipant($id);
    if ($p) {
        foreach (['photo','carte_identite'] as $col) {
            if (!empty($p[$col])) {
                $f = __DIR__ . '/../assets/img/uploads/' . $p[$col];
                if (file_exists($f)) unlink($f);
            }
        }
    }
    getPDO()->prepare("DELETE FROM participants WHERE id=?")->execute([$id]);
}
function selectParticipant($id, $adminId) {
    $pdo = getPDO();
    $pdo->prepare("UPDATE participants SET statut='selectionne' WHERE id=?")->execute([$id]);
    $c = $pdo->prepare("SELECT id FROM selections WHERE participant_id=?");
    $c->execute([$id]);
    if (!$c->fetch()) {
        $pdo->prepare("INSERT INTO selections (participant_id,admin_id) VALUES (?,?)")->execute([$id,$adminId]);
    }
}
function rejectParticipant($id) {
    getPDO()->prepare("UPDATE participants SET statut='rejete' WHERE id=?")->execute([$id]);
}
function uploadPhoto($file, $prefix = 'p_') {
    $dir = __DIR__ . '/../assets/img/uploads/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $allowed = ['image/jpeg','image/png','image/webp','image/jpg'];
    if (!in_array($file['type'], $allowed) || $file['size'] > 8*1024*1024) return false;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'])) return false;
    $fn = $prefix . uniqid() . '.' . $ext;
    return move_uploaded_file($file['tmp_name'], $dir.$fn) ? $fn : false;
}

/* ── Admins ── */
function getAdmins() {
    return getPDO()->query("SELECT id,nom,email,role,date_creation FROM admins ORDER BY role DESC,date_creation ASC")->fetchAll();
}
function createAdmin($nom, $email, $pass, $role) {
    $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost'=>12]);
    getPDO()->prepare("INSERT INTO admins (nom,email,password,role) VALUES (?,?,?,?)")->execute([$nom,$email,$hash,$role]);
}
function deleteAdmin($id) {
    getPDO()->prepare("DELETE FROM admins WHERE id=? AND role!='superadmin'")->execute([$id]);
}

/* Mappings */
$STATUT_BADGE = ['en_attente'=>'badge-wait','selectionne'=>'badge-sel','rejete'=>'badge-rej'];
$STATUT_LABEL = ['en_attente'=>'En attente','selectionne'=>'Sélectionné','rejete'=>'Rejeté'];

// Compatibilité : définir aussi comme constantes pour les fichiers admin
if (!defined('STATUT_BADGE')) {
    define('STATUT_BADGE', ['en_attente'=>'badge-wait','selectionne'=>'badge-sel','rejete'=>'badge-rej']);
    define('STATUT_LABEL', ['en_attente'=>'En attente','selectionne'=>'Sélectionné','rejete'=>'Rejeté']);
}
