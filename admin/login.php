<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!empty($_SESSION['admin_id'])) { header('Location: ' . $BASE . '/admin/dashboard.php'); exit; }
require_once __DIR__ . '/../includes/db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if ($email && $pass) {
        $s = getPDO()->prepare("SELECT * FROM admins WHERE email=?");
        $s->execute([$email]);
        $a = $s->fetch();
        if ($a && password_verify($pass, $a['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $a['id'];
            $_SESSION['admin_nom']  = $a['nom'];
            $_SESSION['admin_role'] = $a['role'];
            header('Location: ' . $BASE . '/admin/dashboard.php'); exit;
        }
        $error = 'Email ou mot de passe incorrect.';
    } else { $error = 'Remplissez tous les champs.'; }
}
$pageTitle = 'Admin — Shopping Date';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= $pageTitle ?></title>
  <link rel="icon" href="<?= $BASE ?>/assets/img/logo.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?= $BASE ?>/assets/css/style.css">
</head>
<body>
<div id="scroll-prog"></div>
<div class="admin-login-wrap">
  <div class="admin-login-card">
    <div class="admin-login-logo">
      <img src="<?= $BASE ?>/assets/img/logo.png" alt="Logo" style="max-height:60px;margin:0 auto 14px;display:block" onerror="this.style.display='none'">
      <p style="color:var(--muted);font-size:.68rem;letter-spacing:2px;margin-top:4px">Espace Administration</p>
    </div>
    <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom:18px"><span>⚠️</span> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="admin@shoppingdate.com"
               value="<?= htmlspecialchars($_POST['email']??'') ?>" required autofocus autocomplete="username">
      </div>
      <div class="form-group" style="margin-bottom:26px">
        <label class="form-label" for="password">Mot de passe</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
    </form>
    <p style="text-align:center;margin-top:18px;font-size:.74rem">
      <a href="<?= $BASE ?>/" style="color:var(--gray)">← Retour au site public</a>
    </p>
  </div>
</div>
<script src="<?= $BASE ?>/assets/js/main.js"></script>
</body></html>
