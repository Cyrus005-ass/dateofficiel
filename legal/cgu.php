<?php
$pageTitle = 'CGU | Shopping Date';

require_once __DIR__ . '/../includes/header.php';
?>
<main class="legal-page">
<div class="legal-banner"><div class="container"><div class="section-eyebrow" style="margin:0 auto 12px"><span class="eyebrow-dot"></span>Légal</div><h1>CONDITIONS GÉNÉRALES</h1><p>Mise à jour : <?php echo date('d/m/Y'); ?></p></div></div>
<div class="legal-content">
<h2>1. Présentation</h2><p>Shopping Date est une émission de téléréalité produite par <strong>Muse Origin Studio et Elite Event Agency</strong>, dont le principe est de réunir des célibataires autour d'une expérience shopping et d'une première rencontre filmée.</p>
<h2>2. Conditions d'éligibilité</h2><ul><li>Avoir <strong>18 ans minimum</strong> à la date d'inscription</li><li>Être célibataire au moment de l'inscription</li><li>Résider dans la zone géographique couverte par la production</li><li>Accepter d'être filmé(e) pour l'émission et ses supports promotionnels</li></ul>
<h2>3. Droits à l'image</h2><p>En s'inscrivant, le participant autorise Muse Origin Studio et Elite Event Agency à utiliser son image, sa voix et ses déclarations dans le cadre de l'émission Shopping Date et de sa promotion, sans contrepartie financière supplémentaire au budget shopping offert.</p>
<h2>4. Budget shopping</h2><p>Les participants sélectionnés reçoivent un budget de <strong>10 000 FCFA</strong> pour préparer leur look. Ce montant ne peut être remboursé en cas de désistement après confirmation de la sélection.</p>
<h2>5. Sélection des participants</h2><p>La sélection est réalisée selon des critères définis par l'équipe de production. Aucun recours n'est possible en cas de non-sélection. Muse Origin Studio et Elite Event Agency se réservent le droit de modifier le nombre de participants sans préavis.</p>
<h2>6. Données personnelles</h2><p>Vos données sont traitées conformément à notre <a href="<?= $BASE ?>/legal/privacy.php" style="color:var(--pink)">Politique de confidentialité</a>.</p>
<h2>7. Contact</h2><p>Pour toute question : <a href="mailto:<?= htmlspecialchars($SITE_LINKS['contact_email']) ?>" style="color:var(--pink);font-weight:700"><?= htmlspecialchars($SITE_LINKS['contact_email']) ?></a></p>
<div style="margin-top:44px;display:flex;gap:14px;flex-wrap:wrap"><a href="<?= $BASE ?>/legal/privacy.php" class="btn btn-ghost">Confidentialité</a><a href="<?= $BASE ?>/inscription/step1.php" class="btn btn-primary">S'inscrire</a></div>
</div></main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
