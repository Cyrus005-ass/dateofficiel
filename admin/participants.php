<?php
// admin/participants.php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();


if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    deleteParticipant((int)$_GET['delete']);
    header('Location: ' . $BASE . '/admin/participants.php?msg=del'); exit;
}
if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
    rejectParticipant((int)$_GET['reject']);
    header('Location: ' . $BASE . '/admin/participants.php?msg=rej'); exit;
}

$statut = $_GET['statut'] ?? '';
$list   = getParticipants($statut);
$stats  = countStats();
$view   = (isset($_GET['view']) && is_numeric($_GET['view'])) ? getParticipant((int)$_GET['view']) : null;
$msg    = $_GET['msg'] ?? '';
?>
<!DOCTYPE html><html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Participants — Admin Shopping Date</title>
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
      <h1>Participants</h1>
      <a href="<?= $BASE ?>/admin/logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
    </div>
    <div class="admin-body">
      <?php if ($msg === 'del'): ?><div class="alert alert-success"><span>OK</span> Participant supprimé.</div>
      <?php elseif ($msg === 'rej'): ?><div class="alert alert-success"><span>OK</span> Participant rejeté.</div><?php endif; ?>

      <?php if ($view): ?>
        <!-- DETAIL -->
        <div style="margin-bottom:16px"><a href="<?= $BASE ?>/admin/participants.php" class="btn btn-ghost btn-sm">← Retour liste</a></div>
        <div class="detail-card">
          <div class="detail-head">
            <?php if($view['photo']): ?>
              <img src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($view['photo']) ?>" class="detail-photo av-preview" alt="" data-full-src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($view['photo']) ?>">
            <?php else: ?>
              <div class="detail-init"><?= initials($view['prenom'],$view['nom']) ?></div>
            <?php endif; ?>
            <div class="detail-info">
              <h2><?= htmlspecialchars($view['prenom'].' '.$view['nom']) ?></h2>
              <p><?= htmlspecialchars($view['email']) ?> · <?= htmlspecialchars($view['telephone']) ?></p>
              <span class="badge <?= STATUT_BADGE[$view['statut']] ?>" style="margin-top:8px"><?= STATUT_LABEL[$view['statut']] ?></span>
            </div>
            <div class="detail-actions">
              <?php if($view['statut']==='en_attente'): ?>
                <a href="<?= $BASE ?>/admin/select.php?id=<?= $view['id'] ?>" class="btn btn-primary btn-sm">✓ Sélectionner</a>
                <a href="?reject=<?= $view['id'] ?>" class="btn btn-ghost btn-sm" onclick="return confirm('Rejeter ce participant ?')">✕ Rejeter</a>
              <?php endif; ?>
              <a href="?delete=<?= $view['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer définitivement ?')">🗑 Supprimer</a>
            </div>
          </div>
          <div class="detail-body">
            <div class="detail-grid">
              <div class="detail-field"><label>Âge</label><p><?= $view['age'] ?> ans</p></div>
              <div class="detail-field"><label>Genre</label><p><?= ucfirst($view['sexe']) ?></p></div>
              <div class="detail-field"><label>Ville</label><p><?= htmlspecialchars($view['ville']) ?></p></div>
              <div class="detail-field"><label>Profession</label><p><?= htmlspecialchars($view['profession']) ?></p></div>
              <div class="detail-field"><label>Inscrit le</label><p><?= date('d/m/Y H:i', strtotime($view['date_inscription'])) ?></p></div>
            </div>
            <div class="detail-grid" style="margin-top:18px">
              <div class="detail-field" style="grid-column:1/-1">
                <label>Description</label>
                <p><?= nl2br(htmlspecialchars($view['description'])) ?></p>
              </div>
              <div class="detail-field" style="grid-column:1/-1">
                <label>Critères recherchés</label>
                <p><?= nl2br(htmlspecialchars($view['partner_criteria'] ?? '')) ?></p>
              </div>
              <div class="detail-field" style="grid-column:1/-1">
                <label>Red flags</label>
                <p><?= nl2br(htmlspecialchars($view['red_flags'] ?? '')) ?></p>
              </div>
              <div class="detail-field" style="grid-column:1/-1">
                <label>Green flags</label>
                <p><?= nl2br(htmlspecialchars($view['green_flags'] ?? '')) ?></p>
              </div>
              <div class="detail-field" style="grid-column:1/-1">
                <label>Rendez-vous idéal</label>
                <p><?= nl2br(htmlspecialchars($view['ideal_date'] ?? '')) ?></p>
              </div>
            </div>
            <div class="detail-grid" style="margin-top:18px">
              <div class="detail-field">
                <label>Photo</label>
                <?php if ($view['photo']): ?>
                  <img src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($view['photo']) ?>" class="detail-doc av-preview" alt="Photo participant" data-full-src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($view['photo']) ?>">
                <?php else: ?>
                  <p>Non fournie</p>
                <?php endif; ?>
              </div>
              <div class="detail-field">
                <label>Carte d identité</label>
                <?php if ($view['carte_identite']): ?>
                  <img src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($view['carte_identite']) ?>" class="detail-doc av-preview" alt="Carte d identite" data-full-src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($view['carte_identite']) ?>">
                <?php else: ?>
                  <p>Non fournie</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

      <?php else: ?>
        <!-- LISTE -->
        <div class="search-row">
          <input type="text" id="searchInp" class="search-input" placeholder="🔍 Rechercher...">
          <a href="?" class="btn btn-sm <?= !$statut?'btn-primary':'btn-ghost' ?>">Tous (<?= $stats['total'] ?>)</a>
          <a href="?statut=en_attente" class="btn btn-sm <?= $statut==='en_attente'?'btn-primary':'btn-ghost' ?>">Attente (<?= $stats['attente'] ?>)</a>
          <a href="?statut=selectionne" class="btn btn-sm <?= $statut==='selectionne'?'btn-primary':'btn-ghost' ?>">Sélectionnés (<?= $stats['sel'] ?>)</a>
          <a href="?statut=rejete" class="btn btn-sm <?= $statut==='rejete'?'btn-primary':'btn-ghost' ?>">Rejetés (<?= $stats['rejete'] ?>)</a>
        </div>
        <div class="table-card">
          <div class="table-head">
            <h3>Participants <span style="color:var(--muted);font-weight:400;font-size:.85rem">(<?= count($list) ?>)</span></h3>
          </div>
          <div class="table-overflow">
            <?php if(empty($list)): ?><div class="empty-state">Aucun participant trouvé.</div>
            <?php else: ?>
            <table><thead><tr><th>Participant</th><th>Email</th><th>Âge / Genre</th><th>Ville</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody id="partBody">
            <?php foreach ($list as $p): ?>
            <tr>
              <td><div class="p-info">
                <?php if($p['photo']): ?><img src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($p['photo']) ?>" class="av av-preview" alt="" data-full-src="<?= $BASE ?>/assets/img/uploads/<?= htmlspecialchars($p['photo']) ?>"><?php else: ?><div class="av-init"><?= initials($p['prenom'],$p['nom']) ?></div><?php endif; ?>
                <div><div class="p-name"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></div><div class="p-sub"><?= htmlspecialchars($p['profession']) ?></div></div>
              </div></td>
              <td style="color:var(--muted);font-size:.8rem"><?= htmlspecialchars($p['email']) ?></td>
              <td><?= $p['age'] ?> · <?= ucfirst($p['sexe']) ?></td>
              <td><?= htmlspecialchars($p['ville']) ?></td>
              <td><span class="badge <?= STATUT_BADGE[$p['statut']] ?>"><?= STATUT_LABEL[$p['statut']] ?></span></td>
              <td style="color:var(--muted);font-size:.78rem"><?= date('d/m/Y', strtotime($p['date_inscription'])) ?></td>
              <td><div class="action-row">
                <a href="?view=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Voir</a>
                <?php if($p['statut']==='en_attente'): ?>
                  <a href="<?= $BASE ?>/admin/select.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">✓</a>
                  <a href="?reject=<?= $p['id'] ?>" class="btn btn-ghost btn-sm" onclick="return confirm('Rejeter ?')">✕</a>
                <?php endif; ?>
                <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">🗑</a>
              </div></td>
            </tr>
            <?php endforeach; ?>
            </tbody></table>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="<?= $BASE ?>/assets/js/main.js"></script>
<script>document.addEventListener('DOMContentLoaded',function(){ tableSearch('searchInp','partBody'); });</script>
<div class="image-lightbox" id="imageLightbox" hidden>
  <button type="button" class="image-lightbox-close" aria-label="Fermer l'aperçu">×</button>
  <img src="" alt="Aperçu agrandi" class="image-lightbox-img" id="imageLightboxImg">
</div>
</body></html>
