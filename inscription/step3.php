<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (empty($_SESSION['step1']) || empty($_SESSION['step2'])) { header('Location: ' . $BASE . '/inscription/step1.php'); exit; }
$pageTitle = 'Inscription — Étape 3 | Shopping Date';

$data = $_SESSION['step3'] ?? [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $criteria = trim($_POST['partner_criteria'] ?? '');
    $redFlags = trim($_POST['red_flags'] ?? '');
    $greenFlags = trim($_POST['green_flags'] ?? '');
    $idealDate = trim($_POST['ideal_date'] ?? '');

    if (strlen($criteria) < 10) $errors['partner_criteria'] = 'Merci de preciser les qualites recherchees.';
    if (strlen($redFlags) < 10) $errors['red_flags'] = 'Merci de preciser les red flags.';
    if (strlen($greenFlags) < 10) $errors['green_flags'] = 'Merci de preciser les green flags.';
    if (strlen($idealDate) < 10) $errors['ideal_date'] = 'Merci de decrire votre rendez-vous ideal.';

    if (empty($errors)) {
        $_SESSION['step3'] = [
            'partner_criteria' => $criteria,
            'red_flags' => $redFlags,
            'green_flags' => $greenFlags,
            'ideal_date' => $idealDate,
        ];
        header('Location: ' . $BASE . '/inscription/step4.php'); exit;
    }

    $data = [
        'partner_criteria' => $criteria,
        'red_flags' => $redFlags,
        'green_flags' => $greenFlags,
        'ideal_date' => $idealDate,
    ];
}
require_once __DIR__ . '/../includes/header.php';
?>
<main class="form-page">
  <div class="form-banner">
    <div class="container">
      <div class="section-eyebrow"><span class="eyebrow-dot"></span>Étape 3 sur 4</div>
      <h1>VOS PREFERENCES</h1>
      <p>Precisez vos attentes pour le rendez-vous</p>
      <div class="progress">
        <div class="prog-step"><div class="prog-circle done">✓</div><div class="prog-label">Identité</div></div>
        <div class="prog-line done"></div>
        <div class="prog-step"><div class="prog-circle done">✓</div><div class="prog-label">Profil</div></div>
        <div class="prog-line done"></div>
        <div class="prog-step"><div class="prog-circle active">3</div><div class="prog-label active">Préférences</div></div>
        <div class="prog-line"></div>
        <div class="prog-step"><div class="prog-circle">4</div><div class="prog-label">Validation</div></div>
      </div>
    </div>
  </div>

  <div class="form-section"><div class="container">
    <div class="form-card sr">
      <h2 class="form-card-title">Étape 3 — <span class="text-grad">Vos preferences</span></h2>
      <p class="form-card-sub">Ces reponses aideront l equipe a mieux comprendre votre vision du match ideal.</p>

      <?php if (!empty($errors)): ?><div class="alert alert-error"><span>Erreur</span> Corrigez les erreurs ci-dessous.</div><?php endif; ?>

      <form method="POST" id="sd-form" novalidate>
        <div class="form-group">
          <label class="form-label" for="partner_criteria">Quels sont les criteres ou qualites que vous recherchez chez un(e) partenaire ? <span>*</span></label>
          <textarea id="partner_criteria" name="partner_criteria" class="form-control <?= isset($errors['partner_criteria'])?'error':'' ?>" data-rules="required,min:10" data-label="Les criteres" maxlength="500"><?= htmlspecialchars($data['partner_criteria'] ?? '') ?></textarea>
          <div class="form-error" id="e_partner_criteria"><?= $errors['partner_criteria'] ?? '' ?></div>
        </div>
        <div class="form-group">
          <label class="form-label" for="red_flags">Quels comportements ou attitudes considerez-vous comme des elements redhibitoires (red flags) ? <span>*</span></label>
          <textarea id="red_flags" name="red_flags" class="form-control <?= isset($errors['red_flags'])?'error':'' ?>" data-rules="required,min:10" data-label="Les red flags" maxlength="500"><?= htmlspecialchars($data['red_flags'] ?? '') ?></textarea>
          <div class="form-error" id="e_red_flags"><?= $errors['red_flags'] ?? '' ?></div>
        </div>
        <div class="form-group">
          <label class="form-label" for="green_flags">A l inverse, quels sont les traits ou valeurs que vous appreciez particulierement (green flags) ? <span>*</span></label>
          <textarea id="green_flags" name="green_flags" class="form-control <?= isset($errors['green_flags'])?'error':'' ?>" data-rules="required,min:10" data-label="Les green flags" maxlength="500"><?= htmlspecialchars($data['green_flags'] ?? '') ?></textarea>
          <div class="form-error" id="e_green_flags"><?= $errors['green_flags'] ?? '' ?></div>
        </div>
        <div class="form-group">
          <label class="form-label" for="ideal_date">Comment imaginez-vous le deroulement d un rendez-vous ideal ? <span>*</span></label>
          <textarea id="ideal_date" name="ideal_date" class="form-control <?= isset($errors['ideal_date'])?'error':'' ?>" data-rules="required,min:10" data-label="Le rendez-vous ideal" maxlength="500"><?= htmlspecialchars($data['ideal_date'] ?? '') ?></textarea>
          <div class="form-error" id="e_ideal_date"><?= $errors['ideal_date'] ?? '' ?></div>
        </div>

        <div class="form-actions">
          <a href="<?= $BASE ?>/inscription/step2.php" class="btn btn-ghost">← Retour</a>
          <button type="submit" class="btn btn-primary">Continuer → Étape 4</button>
        </div>
      </form>
    </div>
  </div></div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
