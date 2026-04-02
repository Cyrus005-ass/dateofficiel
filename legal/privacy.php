<?php
$pageTitle = 'Politique de confidentialité | Shopping Date';

require_once __DIR__ . '/../includes/header.php';
?>
<main class="legal-page">
<div class="legal-banner"><div class="container"><div class="section-eyebrow" style="margin:0 auto 12px"><span class="eyebrow-dot"></span>Légal</div><h1>CONFIDENTIALITÉ</h1><p>Mise à jour : <?php echo date('d/m/Y'); ?></p></div></div>
<div class="legal-content">
<h2>1. Données collectées</h2><p>Lors de votre inscription, nous collectons : nom, prénom, email, téléphone, ville, âge, genre, profession, description personnelle et photo de profil.</p>
<h2>2. Finalité du traitement</h2><p>Ces données sont utilisées exclusivement pour la gestion des candidatures, la sélection des participants et les communications relatives à l'émission Shopping Date.</p>
<h2>3. Sécurité</h2><p>Vos données sont stockées dans une base de données sécurisée avec connexion chiffrée (PDO). Les mots de passe des administrateurs sont hachés avec bcrypt (coût 12). Toutes les sorties sont protégées contre les injections XSS et SQL.</p>
<h2>4. Durée de conservation</h2><ul><li>Participants non sélectionnés : données supprimées dans les 6 mois suivant la fin de saison</li><li>Participants sélectionnés : durée de la production et de la diffusion</li></ul>
<h2>5. Partage des données</h2><p>Vos données ne sont jamais vendues ni partagées à des tiers à des fins commerciales. Elles peuvent être partagées avec les équipes de production directement impliquées dans l'émission.</p>
<h2>6. Vos droits</h2><p>Vous disposez d'un droit d'accès, de rectification et de suppression de vos données. Contactez-nous à : <a href="mailto:<?= htmlspecialchars($SITE_LINKS['contact_email']) ?>" style="color:var(--pink);font-weight:700"><?= htmlspecialchars($SITE_LINKS['contact_email']) ?></a></p>
<div style="margin-top:44px;display:flex;gap:14px;flex-wrap:wrap"><a href="<?= $BASE ?>/legal/cgu.php" class="btn btn-ghost">Retour CGU</a><a href="<?= $BASE ?>/" class="btn btn-primary">Accueil</a></div>
</div></main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
