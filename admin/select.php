<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mail.php';
requireAdmin();

$msg  = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $p = getParticipant((int)$_GET['id']);
    if ($p) {
        selectParticipant((int)$_GET['id'], (int)$_SESSION['admin_id']);
        $result = sendSelectionEmail($p);
        $icon   = $result['ok'] ? 'OK' : 'ERREUR';
        $msg    = $icon . ' ' . htmlspecialchars($p['prenom'] . ' ' . $p['nom']) . ' sélectionné(e). ' . htmlspecialchars($result['msg']);
    }
}
$attente  = getParticipants('en_attente');
$selected = getParticipants('selectionne');
$pdo      = getPDO();
?>
<!DOCTYPE html><html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sélections — Admin Shopping Date</title>
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
      <h1>Sélections</h1>
      <a href="<?= $BASE ?>/admin/logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
    </div>
    <div class="admin-body">
      <?php if ($msg): ?>
      <div class="alert <?= strpos($msg,'✅') === 0 ? 'alert-success' : 'alert-error' ?>" style="margin-bottom:20px">
        <span><?= strpos($msg,'OK') === 0 ? 'OK' : 'ERREUR' ?></span>
        <?= $msg ?>
      </div>
      <?php endif; ?>

      <!-- Participants en attente -->
      <?php if (!empty($attente)): ?>
      <div class="table-card" style="margin-bottom:22px">
        <div class="table-head">
          <h3>En attente de sélection <span style="color:var(--muted);font-weight:400">(<?= count($attente) ?>)</span></h3>
        </div>
        <div class="table-overflow"><table><thead><tr><th>Participant</th><th>Âge / Genre</th><th>Ville</th><th>Inscrit le</th><th>Action</th></tr></thead><tbody>
        <?php foreach ($attente as $p): ?>
        <tr>
          <td><div class="p-info">
            <?php if($p['photo']): ?><img src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($p['photo']) ?>" class="av av-preview" alt="" data-full-src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($p['photo']) ?>"><?php else: ?><div class="av-init"><?= initials($p['prenom'],$p['nom']) ?></div><?php endif; ?>
            <div><div class="p-name"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></div><div class="p-sub"><?= htmlspecialchars($p['email']) ?></div></div>
          </div></td>
          <td><?= $p['age'] ?> · <?= ucfirst($p['sexe']) ?></td>
          <td><?= htmlspecialchars($p['ville']) ?></td>
          <td style="color:var(--muted);font-size:.78rem"><?= date('d/m/Y', strtotime($p['date_inscription'])) ?></td>
          <td><div class="action-row">
            <a href="<?= $BASE ?>/admin/participants.php?view=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Fiche</a>
            <a href="?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm"
               onclick="return confirm('Sélectionner <?= htmlspecialchars(addslashes($p['prenom'])) ?> et lui envoyer l\'email ?')">✓ Sélectionner</a>
          </div></td>
        </tr>
        <?php endforeach; ?>
        </tbody></table></div>
      </div>
      <?php else: ?>
      <div class="table-card" style="margin-bottom:22px"><div class="empty-state">Tous les participants ont été traites.</div></div>
      <?php endif; ?>

      <!-- Sélectionnés -->
      <div class="table-card">
        <div class="table-head"><h3>Sélectionnés <span style="color:var(--muted);font-weight:400">(<?= count($selected) ?>)</span></h3></div>
        <div class="table-overflow">
          <?php if (empty($selected)): ?><div class="empty-state">Aucun participant sélectionné.</div>
          <?php else: ?>
          <table><thead><tr><th>Participant</th><th>Email</th><th>Genre</th><th>Sélectionné le</th><th>Email envoyé</th></tr></thead><tbody>
          <?php foreach ($selected as $p):
            $sel = $pdo->prepare("SELECT * FROM selections WHERE participant_id=?");
            $sel->execute([$p['id']]); $sd = $sel->fetch(); ?>
          <tr>
            <td><div class="p-info">
              <?php if($p['photo']): ?><img src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($p['photo']) ?>" class="av av-preview" alt="" data-full-src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($p['photo']) ?>"><?php else: ?><div class="av-init"><?= initials($p['prenom'],$p['nom']) ?></div><?php endif; ?>
              <div class="p-name"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></div>
            </div></td>
            <td style="color:var(--muted);font-size:.8rem"><?= htmlspecialchars($p['email']) ?></td>
            <td><?= ucfirst($p['sexe']) ?></td>
            <td style="color:var(--muted);font-size:.78rem"><?= $sd ? date('d/m/Y H:i', strtotime($sd['date_selection'])) : '—' ?></td>
            <td><?php if($sd && $sd['notification_sent']): ?><span class="badge badge-sel">✓ Envoyé</span><?php else: ?><span class="badge badge-pend">En attente</span><?php endif; ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody></table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?= $BASE ?>/assets/js/main.js"></script>
<div class="image-lightbox" id="imageLightbox" hidden>
  <button type="button" class="image-lightbox-close" aria-label="Fermer l'aperçu">×</button>
  <img src="" alt="Aperçu agrandi" class="image-lightbox-img" id="imageLightboxImg">
</div>
</body></html>
