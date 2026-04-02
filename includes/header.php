<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php'; // définit $BASE et les constantes DB
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#070707">
  <meta name="description" content="Shopping Date — L'émission qui réunit des célibataires autour du shopping et d'un rendez-vous. Produite par Muse Origin Studio et Elite Event Agency.">
  <title><?= htmlspecialchars($pageTitle ?? 'Shopping Date — Muse Origin Studio et Elite Event Agency') ?></title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= $BASE ?>/assets/img/logo.png">

  <!-- Google Fonts (avec display=swap pour perf) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- ★ FICHIER CSS GLOBAL — lié sur toutes les pages ★ -->
  <link rel="stylesheet" href="<?= $BASE ?>/assets/css/style.css">

  <?php if (isset($extraHead)) echo $extraHead; ?>
</head>
<body>

<!-- Barre progression scroll -->
<div id="scroll-prog"></div>

<!-- ══ NAVBAR ══ -->
<nav class="navbar" id="navbar">
  <div class="container">

    <a href="<?= $BASE ?>/" class="nav-logo" aria-label="Shopping Date">
      <img src="<?= $BASE ?>/assets/img/logo.png" alt="Shopping Date Logo"
           style="height:48px;width:auto;object-fit:contain"
           onerror="this.outerHTML='<span class=\'brand-fallback brand-fallback-nav\'>SHOPPING DATE</span>'">
    </a>

    <!-- Liens desktop -->
    <ul class="nav-links" id="navLinks">
      <li><a href="<?= $BASE ?>/#concept">Le Concept</a></li>
      <li><a href="<?= $BASE ?>/#steps">Comment ça marche</a></li>
      <li><a href="<?= $BASE ?>/#studio">Le Studio</a></li>
      <li>
        <a href="<?= $BASE ?>/inscription/step1.php" class="btn btn-primary btn-sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          S'inscrire
        </a>
      </li>
    </ul>

    <!-- Burger mobile -->
    <button class="nav-burger" id="burger" aria-label="Ouvrir le menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

  </div>
</nav>

<!-- ══ MENU OVERLAY MOBILE ══ -->
<div class="nav-overlay" id="navOverlay" role="dialog" aria-modal="true">
  <a href="<?= $BASE ?>/#concept">Le Concept</a>
  <a href="<?= $BASE ?>/#steps">Comment ça marche</a>
  <a href="<?= htmlspecialchars($SITE_LINKS['season1']) ?>" target="_blank" rel="noopener">Voir la saison 1</a>
  <a href="<?= $BASE ?>/#faq">FAQ</a>
  <a href="<?= $BASE ?>/#studio">Le Studio</a>
  <a href="<?= $BASE ?>/inscription/step1.php" class="btn btn-primary">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
    S'inscrire gratuitement
  </a></div>
