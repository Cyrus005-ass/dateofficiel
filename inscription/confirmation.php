<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (empty($_SESSION['confirmed'])) { header('Location: ' . $BASE . '/inscription/step1.php'); exit; }
unset($_SESSION['confirmed']);
$pageTitle = 'Inscription confirmée | Shopping Date';

require_once __DIR__ . '/../includes/header.php';
?>
<main style="min-height:calc(100vh - 80px);padding-top:80px;background:var(--black)">
  <div class="confirm-wrap">
    <div class="confirm-card">
      <!-- Check animé -->
      <div class="confirm-check">
        <svg width="42" height="42" viewBox="0 0 48 48" fill="none">
          <path d="M10 26L20 36L38 14" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"
                stroke-dasharray="100" stroke-dashoffset="100"/>
        </svg>
      </div>
      <div class="section-eyebrow" style="margin:0 auto 16px"><span class="eyebrow-dot"></span>Inscription recue</div>
      <h1 class="confirm-title">BIENVENUE !</h1>
      <p class="confirm-sub">
        Votre dossier a bien été enregistré. Notre équipe l'examinera dans les meilleurs délais.
        Si votre profil est retenu, vous recevrez un <strong style="color:var(--pink)">email de confirmation</strong>
        avec tous les détails pratiques.
      </p>
      <div class="confirm-box">
        <div style="text-align:center"><div class="confirm-num">10K</div><div class="confirm-lbl">Budget global</div></div>
        <div style="text-align:center"><div class="confirm-num">SHOP</div><div class="confirm-lbl">Shopping</div></div>
        <div style="text-align:center"><div class="confirm-num">DATE</div><div class="confirm-lbl">Rendez-vous</div></div>
      </div>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="<?= $BASE ?>/" class="btn btn-ghost">← Retour à l'accueil</a>
        <a href="<?= htmlspecialchars($SITE_LINKS['youtube_subscribe']) ?>" target="_blank" rel="noopener" class="btn btn-primary">Suivre la chaine</a>
      </div>
      <p style="margin-top:24px;font-size:.7rem;color:#2a2a2a">Shopping Date — Muse Origin Studio et Elite Event Agency</p>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
