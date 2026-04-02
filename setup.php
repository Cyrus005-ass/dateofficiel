<?php
/**
 * SHOPPING DATE — setup.php
 * Ajouter un admin ou superadmin dans la base de données.
 * ⚠️ Réservé à l'administrateur — ne pas laisser accessible en production.
 */
require_once __DIR__ . '/includes/db.php';

$msg   = '';
$type  = ''; // 'ok' | 'error'
$admins = [];

// ── Récupérer la liste des admins existants ───────────
try {
    $admins = getPDO()->query("SELECT id,nom,email,role,date_creation FROM admins ORDER BY role DESC,id ASC")->fetchAll();
} catch (\Exception $e) {
    $msg  = 'Impossible de se connecter à la base de données : ' . $e->getMessage();
    $type = 'error';
}

// ── Traitement formulaire ajout ───────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'add') {
        $nom   = trim($_POST['nom']   ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password']   ?? '';
        $pass2 = $_POST['password2']  ?? '';
        $role  = in_array($_POST['role']??'', ['admin','superadmin']) ? $_POST['role'] : 'admin';

        if (strlen($nom) < 2)
            { $msg = 'Le nom doit contenir au moins 2 caractères.'; $type='error'; }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
            { $msg = 'Adresse email invalide.'; $type='error'; }
        elseif (strlen($pass) < 8)
            { $msg = 'Mot de passe trop court (8 caractères minimum).'; $type='error'; }
        elseif ($pass !== $pass2)
            { $msg = 'Les mots de passe ne correspondent pas.'; $type='error'; }
        else {
            try {
                $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost'=>12]);
                getPDO()->prepare("INSERT INTO admins (nom,email,password,role) VALUES (?,?,?,?)")
                        ->execute([$nom, $email, $hash, $role]);
                $msg  = '✅ Admin <strong>' . htmlspecialchars($nom) . '</strong> (' . htmlspecialchars($email) . ') créé avec le rôle <strong>' . $role . '</strong>.';
                $type = 'ok';
                // Rafraîchir la liste
                $admins = getPDO()->query("SELECT id,nom,email,role,date_creation FROM admins ORDER BY role DESC,id ASC")->fetchAll();
            } catch (\Exception $e) {
                $msg  = 'Erreur : ' . (strpos($e->getMessage(),'Duplicate') !== false ? 'Cet email est déjà utilisé.' : $e->getMessage());
                $type = 'error';
            }
        }
    }

    if ($_POST['action'] === 'delete' && !empty($_POST['del_id'])) {
        $delId = (int)$_POST['del_id'];
        try {
            // Protéger le dernier superadmin
            $isSup = getPDO()->prepare("SELECT role FROM admins WHERE id=?");
            $isSup->execute([$delId]);
            $row = $isSup->fetch();
            if ($row && $row['role'] === 'superadmin') {
                $countSup = (int)getPDO()->query("SELECT COUNT(*) FROM admins WHERE role='superadmin'")->fetchColumn();
                if ($countSup <= 1) { $msg='Impossible de supprimer le dernier superadmin.'; $type='error'; goto end; }
            }
            getPDO()->prepare("DELETE FROM admins WHERE id=?")->execute([$delId]);
            $msg  = '✅ Admin supprimé.';
            $type = 'ok';
            $admins = getPDO()->query("SELECT id,nom,email,role,date_creation FROM admins ORDER BY role DESC,id ASC")->fetchAll();
        } catch (\Exception $e) {
            $msg = 'Erreur suppression : ' . $e->getMessage(); $type='error';
        }
    }
}
end:
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#070707">
<title>Gestion Admins — Shopping Date</title>
<link rel="icon" href="/assets/img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
<style>
/* ── Reset & Base ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --pink:#FF1E9E;--purple:#F057FF;--red:#FF0A10;
  --bg:#070707;--dark1:#0d0d0d;--dark2:#111;--dark3:#161616;
  --border:rgba(255,255,255,.07);--border-pink:rgba(255,30,158,.25);
  --text:#fff;--muted:#888;--light:#ccc;
  --g:linear-gradient(135deg,#FF1E9E,#F057FF,#FF0A10);
  --r:12px;--r-lg:18px;
  --ease:cubic-bezier(.4,0,.2,1);
}
html{font-size:16px;-webkit-text-size-adjust:100%}
body{
  font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);
  min-height:100vh;padding:0;
  background-image:radial-gradient(ellipse at 50% 0%,rgba(255,30,158,.06) 0%,transparent 60%);
}

/* ── Layout ── */
.page{max-width:860px;margin:0 auto;padding:clamp(20px,5vw,48px) clamp(14px,4vw,24px) 60px}

/* ── Header ── */
.page-header{text-align:center;margin-bottom:36px;padding-bottom:28px;border-bottom:1px solid var(--border)}
.page-header img{height:clamp(40px,8vw,56px);margin-bottom:14px;object-fit:contain}
.page-header h1{font-family:'Bebas Neue',cursive;font-size:clamp(1.8rem,5vw,2.6rem);letter-spacing:5px;background:var(--g);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.page-header p{color:var(--muted);font-size:.78rem;letter-spacing:2px;text-transform:uppercase;margin-top:6px}

/* ── Alert ── */
.alert{display:flex;align-items:flex-start;gap:12px;border-radius:var(--r);padding:14px 18px;margin-bottom:22px;font-size:.88rem;line-height:1.6}
.alert-ok {background:rgba(95,255,170,.07);border:1px solid rgba(95,255,170,.25);color:#5fffaa}
.alert-error{background:rgba(255,10,16,.07);border:1px solid rgba(255,10,16,.25);color:#ff7070}

/* ── Panels ── */
.panel{background:var(--dark2);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;margin-bottom:24px}
.panel-head{display:flex;align-items:center;gap:12px;padding:18px 22px;border-bottom:1px solid var(--border);background:var(--dark1)}
.panel-head h2{font-family:'Bebas Neue',cursive;font-size:1.2rem;letter-spacing:3px;margin:0}
.panel-head .badge{background:var(--g);color:#fff;font-size:.62rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:1px}
.panel-body{padding:22px}

/* ── Form ── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-group.full{grid-column:1/-1}
label.lbl{font-size:.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--muted)}
label.lbl span{color:var(--pink);margin-left:2px}
input,select{
  width:100%;background:var(--dark3);
  border:1px solid var(--border);border-radius:var(--r);
  padding:12px 15px;color:var(--text);
  font-family:'DM Sans',sans-serif;font-size:.88rem;
  outline:none;transition:border-color .25s,box-shadow .25s;
  -webkit-appearance:none;appearance:none;
}
input:focus,select:focus{border-color:var(--pink);box-shadow:0 0 0 3px rgba(255,30,158,.1)}
select option{background:var(--dark2)}
.pwd-wrap{position:relative}
.pwd-wrap input{padding-right:44px}
.pwd-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;font-size:1.1rem;line-height:1}
.pwd-rules{display:flex;flex-wrap:wrap;gap:6px;margin-top:6px}
.pwd-rule{font-size:.65rem;padding:3px 8px;border-radius:6px;background:var(--dark3);border:1px solid var(--border);color:var(--muted);transition:all .2s}
.pwd-rule.ok{background:rgba(95,255,170,.08);border-color:rgba(95,255,170,.3);color:#5fffaa}
.role-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.role-card{border:1.5px solid var(--border);border-radius:var(--r);padding:14px 15px;cursor:pointer;transition:all .25s;position:relative}
.role-card input[type=radio]{position:absolute;opacity:0;width:0;height:0}
.role-card:has(input:checked){border-color:var(--pink);background:rgba(255,30,158,.06)}
.role-card-title{font-size:.78rem;font-weight:700;color:var(--text);margin-bottom:4px}
.role-card-desc{font-size:.68rem;color:var(--muted);line-height:1.5}
.role-card-badge{display:inline-block;font-size:.55rem;padding:2px 7px;border-radius:10px;font-weight:700;letter-spacing:1px;margin-top:4px}
.badge-admin{background:rgba(255,30,158,.12);color:var(--pink);border:1px solid var(--border-pink)}
.badge-super{background:rgba(240,87,255,.15);color:var(--purple);border:1px solid rgba(240,87,255,.3)}
.btn-submit{
  display:flex;align-items:center;justify-content:center;gap:8px;
  width:100%;padding:15px;margin-top:8px;
  background:var(--g);background-size:200%;
  color:#fff;border:none;border-radius:50px;
  font-family:'DM Sans',sans-serif;font-weight:700;font-size:.82rem;
  letter-spacing:1.8px;text-transform:uppercase;cursor:pointer;
  transition:transform .3s,box-shadow .3s;min-height:48px;
}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 14px 40px rgba(255,30,158,.5)}
.btn-submit:active{transform:scale(.97)}

/* ── Table admins ── */
.admins-table{width:100%;border-collapse:collapse}
.admins-table th{font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--muted);padding:10px 14px;border-bottom:1px solid var(--border);text-align:left}
.admins-table td{padding:13px 14px;border-bottom:1px solid rgba(255,255,255,.04);font-size:.85rem;vertical-align:middle}
.admins-table tr:last-child td{border-bottom:none}
.admins-table tr:hover td{background:rgba(255,255,255,.02)}
.role-pill{display:inline-flex;align-items:center;gap:5px;font-size:.62rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:1px;text-transform:uppercase}
.pill-admin{background:rgba(255,30,158,.1);color:var(--pink);border:1px solid var(--border-pink)}
.pill-super{background:rgba(240,87,255,.12);color:var(--purple);border:1px solid rgba(240,87,255,.25)}
.btn-del{
  display:inline-flex;align-items:center;justify-content:center;
  background:rgba(255,10,16,.08);border:1px solid rgba(255,10,16,.2);
  color:#ff7070;border-radius:8px;padding:6px 12px;
  font-size:.7rem;font-weight:700;cursor:pointer;
  transition:all .2s;min-height:34px;min-width:34px;
}
.btn-del:hover{background:rgba(255,10,16,.18);border-color:rgba(255,10,16,.4)}
.no-admins{text-align:center;padding:30px;color:var(--muted);font-size:.88rem}

/* ── Warning box ── */
.warn-box{
  display:flex;gap:10px;align-items:flex-start;
  background:rgba(255,165,0,.07);border:1px solid rgba(255,165,0,.2);
  border-radius:var(--r);padding:13px 16px;margin-top:18px;
  font-size:.74rem;color:#ffb347;line-height:1.65;
}
.warn-box svg{flex-shrink:0;margin-top:1px}
.home-link{display:block;text-align:center;margin-top:22px;color:var(--muted);font-size:.78rem;text-decoration:none}
.home-link:hover{color:var(--pink)}

/* ── Responsive ── */
@media(max-width:640px){
  .form-grid{grid-template-columns:1fr}
  .form-group.full{grid-column:1}
  .role-grid{grid-template-columns:1fr}
  .admins-table th:nth-child(4),.admins-table td:nth-child(4){display:none}
  .panel-head{padding:14px 16px}
  .panel-body{padding:16px}
}
@media(max-width:380px){
  .page-header h1{font-size:1.6rem;letter-spacing:3px}
  .admins-table th:nth-child(3),.admins-table td:nth-child(3){display:none}
}
</style>
</head>
<body>
<div class="page">

  <!-- Header -->
  <div class="page-header">
    <img src="/assets/img/logo.png" alt="Logo Shopping Date" onerror="this.style.display='none'">
    <h1>GESTION ADMINS</h1>
    <p>Shopping Date · Muse Origin Studio</p>
  </div>

  <!-- Alert -->
  <?php if ($msg): ?>
  <div class="alert alert-<?= $type === 'ok' ? 'ok' : 'error' ?>">
    <?= $type === 'ok' ? '✅' : '⚠️' ?>
    <span><?= $msg ?></span>
  </div>
  <?php endif; ?>

  <!-- ═══ PANEL : Ajouter un admin ═══ -->
  <div class="panel">
    <div class="panel-head">
      <h2>AJOUTER UN ADMIN</h2>
      <span class="badge">NOUVEAU</span>
    </div>
    <div class="panel-body">
      <form method="POST" id="addForm" autocomplete="off" novalidate>
        <input type="hidden" name="action" value="add">
        <div class="form-grid">

          <div class="form-group">
            <label class="lbl" for="nom">Nom complet <span>*</span></label>
            <input type="text" id="nom" name="nom" placeholder="Ex : Jean Dupont"
                   value="<?= htmlspecialchars($_POST['nom']??'') ?>"
                   required minlength="2" autocomplete="off">
          </div>

          <div class="form-group">
            <label class="lbl" for="email">Adresse email <span>*</span></label>
            <input type="email" id="email" name="email" placeholder="admin@exemple.com"
                   value="<?= htmlspecialchars($_POST['email']??'') ?>"
                   required autocomplete="off">
          </div>

          <div class="form-group">
            <label class="lbl" for="password">Mot de passe <span>*</span></label>
            <div class="pwd-wrap">
              <input type="password" id="password" name="password"
                     placeholder="Minimum 8 caractères"
                     required minlength="8" autocomplete="new-password"
                     oninput="checkPwd(this.value)">
              <button type="button" class="pwd-toggle" onclick="togglePwd('password',this)" title="Afficher/Masquer">👁</button>
            </div>
            <div class="pwd-rules">
              <span id="r-len" class="pwd-rule">8+ chars</span>
              <span id="r-maj" class="pwd-rule">Majuscule</span>
              <span id="r-num" class="pwd-rule">Chiffre</span>
            </div>
          </div>

          <div class="form-group">
            <label class="lbl" for="password2">Confirmer le mot de passe <span>*</span></label>
            <div class="pwd-wrap">
              <input type="password" id="password2" name="password2"
                     placeholder="Répétez le mot de passe"
                     required minlength="8" autocomplete="new-password">
              <button type="button" class="pwd-toggle" onclick="togglePwd('password2',this)" title="Afficher/Masquer">👁</button>
            </div>
          </div>

          <div class="form-group full">
            <label class="lbl">Rôle <span>*</span></label>
            <div class="role-grid">
              <label class="role-card">
                <input type="radio" name="role" value="admin" <?= ($_POST['role']??'admin')==='admin'?'checked':'' ?>>
                <div class="role-card-title">Administrateur</div>
                <div class="role-card-desc">Gestion des participants, sélection, envoi d'emails.</div>
                <span class="role-card-badge badge-admin">ADMIN</span>
              </label>
              <label class="role-card">
                <input type="radio" name="role" value="superadmin" <?= ($_POST['role']??'')==='superadmin'?'checked':'' ?>>
                <div class="role-card-title">Super Administrateur</div>
                <div class="role-card-desc">Accès complet + gestion des administrateurs.</div>
                <span class="role-card-badge badge-super">SUPERADMIN</span>
              </label>
            </div>
          </div>

        </div><!-- /form-grid -->

        <button type="submit" class="btn-submit">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13H13v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
          Créer le compte admin
        </button>
      </form>
    </div>
  </div>

  <!-- ═══ PANEL : Admins existants ═══ -->
  <div class="panel">
    <div class="panel-head">
      <h2>ADMINS EXISTANTS</h2>
      <span class="badge"><?= count($admins) ?></span>
    </div>
    <div class="panel-body" style="padding:0">
      <?php if (empty($admins)): ?>
        <div class="no-admins">Aucun administrateur enregistré.</div>
      <?php else: ?>
      <div style="overflow-x:auto;-webkit-overflow-scrolling:touch">
        <table class="admins-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Créé le</th>
              <th style="width:50px"></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($admins as $a): ?>
            <tr>
              <td><strong><?= htmlspecialchars($a['nom']) ?></strong></td>
              <td style="color:var(--muted)"><?= htmlspecialchars($a['email']) ?></td>
              <td>
                <span class="role-pill <?= $a['role']==='superadmin'?'pill-super':'pill-admin' ?>">
                  <?= $a['role']==='superadmin' ? '★ Super' : 'Admin' ?>
                </span>
              </td>
              <td style="color:var(--muted);font-size:.78rem">
                <?= date('d/m/Y', strtotime($a['date_creation'])) ?>
              </td>
              <td>
                <?php if($a['role'] !== 'superadmin'): ?>
                <form method="POST" onsubmit="return confirm('Supprimer cet admin ?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="del_id" value="<?= $a['id'] ?>">
                  <button type="submit" class="btn-del" title="Supprimer">🗑</button>
                </form>
                <?php else: ?>
                <span style="color:var(--muted);font-size:.7rem">protégé</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Warning -->
  <div class="warn-box">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="#ffb347"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
    <span>Ce fichier doit rester <strong>inaccessible au public</strong> en production. Protégez-le par mot de passe serveur ou supprimez-le après utilisation.</span>
  </div>

  <a href="/admin/login.php" class="home-link">→ Aller à l'interface d'administration</a>

</div><!-- /page -->

<script>
function checkPwd(v) {
  document.getElementById('r-len').className = 'pwd-rule' + (v.length >= 8 ? ' ok' : '');
  document.getElementById('r-maj').className = 'pwd-rule' + (/[A-Z]/.test(v) ? ' ok' : '');
  document.getElementById('r-num').className = 'pwd-rule' + (/[0-9]/.test(v) ? ' ok' : '');
}
function togglePwd(id, btn) {
  var inp = document.getElementById(id);
  if (inp.type === 'password') { inp.type = 'text'; btn.textContent = '🙈'; }
  else { inp.type = 'password'; btn.textContent = '👁'; }
}
// Validation basique avant envoi
document.getElementById('addForm').addEventListener('submit', function(e) {
  var p  = document.getElementById('password').value;
  var p2 = document.getElementById('password2').value;
  if (p !== p2) { e.preventDefault(); alert('Les mots de passe ne correspondent pas.'); }
});
</script>
</body>
</html>
