<?php
// admin/admins.php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

if ($_SESSION['admin_role'] !== 'superadmin') { header('Location: ' . $BASE . '/admin/dashboard.php'); exit; }

$msg = ''; $err = '';
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && (int)$_GET['delete'] !== (int)$_SESSION['admin_id']) {
    deleteAdmin((int)$_GET['delete']);
    $msg = 'Administrateur supprimé.';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom  = trim($_POST['nom'] ?? '');
    $eml  = trim($_POST['email'] ?? '');
    $pwd  = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? '', ['admin','superadmin']) ? $_POST['role'] : 'admin';
    if (strlen($nom)<2 || !filter_var($eml,FILTER_VALIDATE_EMAIL) || strlen($pwd)<8) {
        $err = 'Données invalides (nom ≥ 2 car., email valide, mot de passe ≥ 8 car.)';
    } else {
        try { createAdmin($nom,$eml,$pwd,$role); $msg = 'Admin « '.$nom.' » créé.'; }
        catch (\Exception $e) { $err = 'Cet email est déjà utilisé.'; }
    }
}
$admins = getAdmins();
?>
<!DOCTYPE html><html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Administrateurs — Shopping Date</title>
<link rel="icon" href="<?= $BASE ?>/assets/img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="<?= $BASE ?>/assets/css/style.css">
</head><body>
<div id="scroll-prog"></div>
<div class="admin-wrap">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar">
      <h1>Administrateurs</h1>
      <a href="<?= $BASE ?>/admin/logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
    </div>
    <div class="admin-body">
      <?php if ($msg): ?><div class="alert alert-success" style="margin-bottom:20px"><span>✓</span> <?= htmlspecialchars($msg) ?></div><?php endif; ?>
      <?php if ($err): ?><div class="alert alert-error" style="margin-bottom:20px"><span>⚠️</span> <?= htmlspecialchars($err) ?></div><?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:22px;align-items:start">

        <!-- Formulaire création -->
        <div class="table-card">
          <div class="table-head"><h3>Créer un admin</h3></div>
          <div style="padding:24px">
            <form method="POST">
              <div class="form-group">
                <label class="form-label">Nom complet <span style="color:var(--pink)">*</span></label>
                <input type="text" name="nom" class="form-control" placeholder="Nom complet" required minlength="2">
              </div>
              <div class="form-group">
                <label class="form-label">Email <span style="color:var(--pink)">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="email@domaine.com" required>
              </div>
              <div class="form-group">
                <label class="form-label">Mot de passe <span style="color:var(--pink)">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Minimum 8 caractères" required minlength="8">
              </div>
              <div class="form-group" style="margin-bottom:24px">
                <label class="form-label">Rôle</label>
                <select name="role" class="form-control">
                  <option value="admin">Admin</option>
                  <option value="superadmin">Super Admin</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary btn-full">Créer l'administrateur</button>
            </form>
          </div>
        </div>

        <!-- Liste admins -->
        <div class="table-card">
          <div class="table-head"><h3>Admins existants (<?= count($admins) ?>)</h3></div>
          <div class="table-overflow"><table><thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Créé le</th><th>Action</th></tr></thead><tbody>
          <?php foreach ($admins as $a): ?>
          <tr>
            <td style="font-weight:600"><?= htmlspecialchars($a['nom']) ?></td>
            <td style="color:var(--muted);font-size:.8rem"><?= htmlspecialchars($a['email']) ?></td>
            <td><span class="badge <?= $a['role']==='superadmin'?'badge-sup':'badge-adm' ?>"><?= ucfirst($a['role']) ?></span></td>
            <td style="color:var(--muted);font-size:.78rem"><?= date('d/m/Y', strtotime($a['date_creation'])) ?></td>
            <td>
              <?php if ($a['id'] != $_SESSION['admin_id'] && $a['role'] !== 'superadmin'): ?>
              <a href="?delete=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet admin ?')">✕</a>
              <?php else: ?>
              <span style="color:#2a2a2a;font-size:.75rem">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody></table></div>
        </div>

      </div>

      <!-- Avertissement sécurité -->
      <div style="margin-top:20px;padding:16px 20px;background:rgba(255,30,158,.05);border:1px solid rgba(255,30,158,.15);border-radius:var(--r)">
        <p style="color:var(--muted);font-size:.82rem;line-height:1.7">
          <strong style="color:var(--pink)">⚠️ Sécurité :</strong>
          Changez le mot de passe par défaut immédiatement après l'installation.<br>
          Mot de passe par défaut : <code style="background:var(--dark3);padding:2px 8px;border-radius:5px;color:var(--pink)">password</code>
        </p>
      </div>
    </div>
  </div>
</div>
<script src="<?= $BASE ?>/assets/js/main.js"></script>
</body></html>
