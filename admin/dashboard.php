<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$stats  = countStats();
$recent = array_slice(getParticipants(), 0, 10);

?>
<!DOCTYPE html><html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — Admin Shopping Date</title>
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
      <h1>Dashboard</h1>
      <div class="admin-topbar-right">
        <a href="<?= $BASE ?>/" target="_blank" class="btn btn-ghost btn-sm">↗ Site</a>
        <a href="<?= $BASE ?>/admin/logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
      </div>
    </div>
    <div class="admin-body">
      <!-- Stats -->
      <div class="stats-grid">
        <?php foreach ([
          ['01', $stats['total'],   'Total inscrits'],
          ['02', $stats['attente'], 'En attente'],
          ['03', $stats['sel'],     'Selectionnes'],
          ['04', $stats['total']>0?round($stats['sel']/$stats['total']*100).'%':'0%', 'Taux de selection'],
        ] as $s): ?>
        <div class="stat-card">
          <div class="stat-icon"><?= $s[0] ?></div>
          <div class="stat-num" <?= is_int($s[1])?"data-count='{$s[1]}'" : '' ?>><?= $s[1] ?></div>
          <div class="stat-label"><?= $s[2] ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Tableau -->
      <div class="table-card">
        <div class="table-head">
          <h3>Inscriptions récentes</h3>
          <a href="<?= $BASE ?>/admin/participants.php" class="btn btn-ghost btn-sm">Voir tous →</a>
        </div>
        <div class="table-overflow">
          <?php if (empty($recent)): ?>
          <div class="empty-state">🎯 Aucun participant inscrit pour l'instant.</div>
          <?php else: ?>
          <table><thead><tr><th>Participant</th><th>Email</th><th>Ville</th><th>Âge</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
          <tbody id="recentBody">
          <?php foreach ($recent as $p): ?>
          <tr>
            <td><div class="p-info">
              <?php if($p['photo']): ?><img src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($p['photo']) ?>" class="av av-preview" alt="" data-full-src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($p['photo']) ?>"><?php else: ?><div class="av-init"><?= initials($p['prenom'],$p['nom']) ?></div><?php endif; ?>
              <div><div class="p-name"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></div><div class="p-sub"><?= htmlspecialchars($p['profession']) ?></div></div>
            </div></td>
            <td style="color:var(--muted);font-size:.8rem"><?= htmlspecialchars($p['email']) ?></td>
            <td><?= htmlspecialchars($p['ville']) ?></td>
            <td><?= $p['age'] ?> ans</td>
            <td><span class="badge <?= STATUT_BADGE[$p['statut']] ?>"><?= STATUT_LABEL[$p['statut']] ?></span></td>
            <td style="color:var(--muted);font-size:.78rem"><?= date('d/m/Y', strtotime($p['date_inscription'])) ?></td>
            <td><div class="action-row">
              <a href="<?= $BASE ?>/admin/participants.php?view=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Voir</a>
              <?php if($p['statut']==='en_attente'): ?>
              <a href="<?= $BASE ?>/admin/select.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">✓</a>
              <?php endif; ?>
            </div></td>
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
