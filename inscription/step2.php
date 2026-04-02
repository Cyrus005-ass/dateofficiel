<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (empty($_SESSION['step1'])) { header('Location: ' . $BASE . '/inscription/step1.php'); exit; }
$pageTitle = 'Inscription — Étape 2 | Shopping Date';

$data = $_SESSION['step2'] ?? [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age        = (int)($_POST['age'] ?? 0);
    $sexe       = $_POST['sexe'] ?? '';
    $ville      = trim($_POST['ville'] ?? '');
    $profession = trim($_POST['profession'] ?? '');

    if ($age < 18 || $age > 45)            $errors['age']        = 'Vous devez avoir entre 18 et 45 ans.';
    if (!in_array($sexe,['homme','femme'])) $errors['sexe']       = 'Sélectionnez votre genre.';
    if (strlen($ville) < 2)                $errors['ville']      = 'Indiquez votre ville.';
    if (strlen($profession) < 2)           $errors['profession'] = 'Indiquez votre profession.';

    if (empty($errors)) {
        $_SESSION['step2'] = compact('age','sexe','ville','profession');
        header('Location: ' . $BASE . '/inscription/step3.php'); exit;
    }
    $data = compact('age','sexe','ville','profession');
}
require_once __DIR__ . '/../includes/header.php';
?>
<main class="form-page">
  <div class="form-banner">
    <div class="container">
      <div class="section-eyebrow"><span class="eyebrow-dot"></span>Étape 2 sur 4</div>
      <h1>VOTRE PROFIL</h1>
      <p>Aidez-nous à trouver votre match parfait</p>
      <div class="progress">
        <div class="prog-step"><div class="prog-circle done">✓</div><div class="prog-label">Identité</div></div>
        <div class="prog-line done"></div>
        <div class="prog-step"><div class="prog-circle active">2</div><div class="prog-label active">Profil</div></div>
        <div class="prog-line"></div>
        <div class="prog-step"><div class="prog-circle">3</div><div class="prog-label">Préférences</div></div>
        <div class="prog-line"></div>
        <div class="prog-step"><div class="prog-circle">4</div><div class="prog-label">Validation</div></div>
      </div>
    </div>
  </div>
  <div class="form-section"><div class="container">
    <div class="form-card sr">
      <h2 class="form-card-title">Étape 2 — <span class="text-grad">Votre profil</span></h2>
      <p class="form-card-sub">Ces informations nous aident à trouver le match idéal.</p>
      <?php if (!empty($errors)): ?><div class="alert alert-error"><span>Erreur</span> Corrigez les erreurs.</div><?php endif; ?>
      <form method="POST" id="sd-form" novalidate>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="age">Âge <span>*</span></label>
            <input type="number" id="age" name="age" class="form-control <?= isset($errors['age'])?'error':'' ?>"
                   placeholder="18 – 45 ans" min="18" max="45"
                   value="<?= htmlspecialchars((string)($data['age']??'')) ?>"
                   data-rules="required,age18,age45" data-label="L'âge">
            <div class="form-error" id="e_age"><?= $errors['age']??'' ?></div>
          </div>
          <div class="form-group">
            <label class="form-label" for="sexe">Genre <span>*</span></label>
            <select id="sexe" name="sexe" class="form-control <?= isset($errors['sexe'])?'error':'' ?>"
                    data-rules="required" data-label="Le genre">
              <option value="">— Sélectionnez —</option>
              <option value="homme" <?= ($data['sexe']??'')==='homme'?'selected':'' ?>>Homme</option>
              <option value="femme" <?= ($data['sexe']??'')==='femme'?'selected':'' ?>>Femme</option>
            </select>
            <div class="form-error" id="e_sexe"><?= $errors['sexe']??'' ?></div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="ville">Ville <span>*</span></label>
          <input type="text" id="ville" name="ville" class="form-control <?= isset($errors['ville'])?'error':'' ?>"
                 placeholder="Votre ville de résidence"
                 value="<?= htmlspecialchars($data['ville']??'') ?>"
                 data-rules="required,min:2" data-label="La ville">
          <div class="form-error" id="e_ville"><?= $errors['ville']??'' ?></div>
        </div>
        <div class="form-group">
          <label class="form-label" for="profession">Profession <span>*</span></label>
          <input type="text" id="profession" name="profession" class="form-control <?= isset($errors['profession'])?'error':'' ?>"
                 placeholder="Votre métier ou activité"
                 value="<?= htmlspecialchars($data['profession']??'') ?>"
                 data-rules="required,min:2" data-label="La profession">
          <div class="form-error" id="e_profession"><?= $errors['profession']??'' ?></div>
        </div>
        <div class="form-actions">
          <a href="<?= $BASE ?>/inscription/step1.php" class="btn btn-ghost">← Retour</a>
          <button type="submit" class="btn btn-primary">Continuer → Étape 3</button>
        </div>
      </form>
    </div>
  </div></div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
