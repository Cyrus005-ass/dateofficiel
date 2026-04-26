<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (empty($_SESSION['step1']) || empty($_SESSION['step2']) || empty($_SESSION['step3'])) { header('Location: ' . $BASE . '/inscription/step1.php'); exit; }
$pageTitle = 'Inscription — Étape 4 | Shopping Date';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../includes/functions.php';
    $desc = trim($_POST['description'] ?? '');
    $cgu  = isset($_POST['cgu']);
    if (strlen($desc) < 30) $errors['description'] = 'Description trop courte (minimum 30 caracteres).';
    if (!$cgu) $errors['cgu'] = 'Vous devez accepter les conditions pour continuer.';

    $photo = null;
    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = uploadPhoto($_FILES['photo'], 'p_');
        if (!$photo) $errors['photo'] = 'Photo invalide. Formats acceptes : JPG, PNG, WebP. Max 8 Mo.';
    } else {
        $errors['photo'] = 'Veuillez ajouter une photo de profil.';
    }

    $carteId = null;
    if (!empty($_FILES['carte_identite']['name']) && $_FILES['carte_identite']['error'] === UPLOAD_ERR_OK) {
        $carteId = uploadPhoto($_FILES['carte_identite'], 'ci_');
        if (!$carteId) $errors['carte_identite'] = 'Carte invalide. Formats acceptes : JPG, PNG, WebP. Max 8 Mo.';
    } else {
        $errors['carte_identite'] = 'Veuillez fournir votre carte d identite.';
    }

    if (empty($errors)) {
        require_once __DIR__ . '/../includes/db.php';
        $s1 = $_SESSION['step1'];
        $s2 = $_SESSION['step2'];
        $s3 = $_SESSION['step3'];
        $pdo = getPDO();
        $pdo->prepare("INSERT INTO participants (nom,prenom,email,telephone,sexe,ville,age,profession,partner_criteria,red_flags,green_flags,ideal_date,description,photo,carte_identite) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([
                sanitize($s1['nom']),
                sanitize($s1['prenom']),
                $s1['email'],
                sanitize($s1['telephone']),
                $s2['sexe'],
                sanitize($s2['ville']),
                (int)$s2['age'],
                sanitize($s2['profession']),
                sanitize($s3['partner_criteria']),
                sanitize($s3['red_flags']),
                sanitize($s3['green_flags']),
                sanitize($s3['ideal_date']),
                sanitize($desc),
                $photo,
                $carteId
            ]);
        unset($_SESSION['step1'], $_SESSION['step2'], $_SESSION['step3']);
        $_SESSION['confirmed'] = true;
        header('Location: ' . $BASE . '/inscription/confirmation.php'); exit;
    }
}
require_once __DIR__ . '/../includes/header.php';
?>
<main class="form-page">
  <div class="form-banner">
    <div class="container">
      <div class="section-eyebrow"><span class="eyebrow-dot"></span>Étape 4 sur 4</div>
      <h1>VALIDATION</h1>
      <p>Finalisez votre candidature</p>
      <div class="progress">
        <div class="prog-step"><div class="prog-circle done">✓</div><div class="prog-label">Identité</div></div>
        <div class="prog-line done"></div>
        <div class="prog-step"><div class="prog-circle done">✓</div><div class="prog-label">Profil</div></div>
        <div class="prog-line done"></div>
        <div class="prog-step"><div class="prog-circle done">✓</div><div class="prog-label">Préférences</div></div>
        <div class="prog-line done"></div>
        <div class="prog-step"><div class="prog-circle active">4</div><div class="prog-label active">Validation</div></div>
      </div>
    </div>
  </div>

  <div class="form-section"><div class="container">
    <div class="form-card sr">
      <h2 class="form-card-title">Étape 4 — <span class="text-grad">Photo et verification</span></h2>
      <p class="form-card-sub">Ajoutez vos pieces et finalisez votre inscription.</p>
      <?php if (!empty($errors)): ?><div class="alert alert-error"><span>Erreur</span> Corrigez les erreurs ci-dessous.</div><?php endif; ?>
      <form method="POST" enctype="multipart/form-data" id="sd-form" novalidate>
        <div class="form-group">
          <label class="form-label">Photo de profil <span>*</span></label>
          <div class="upload-zone" id="uploadZone">
            <div class="upload-hint">
              <strong>Cliquez pour choisir votre photo</strong><br>
              <span style="font-size:.72rem;color:var(--gray)">JPG · PNG · WebP · Max 8 Mo</span>
            </div>
            <img id="photoPreview" class="upload-preview" src="" alt="Apercu photo" style="display:none">
          </div>
          <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png,image/webp" style="display:none">
          <div class="form-error"><?= $errors['photo'] ?? '' ?></div>
        </div>
          
        <div class="form-group">
          <label class="form-label" for="description">Parlez de vous <span>*</span></label>
          <textarea id="description" name="description" class="form-control form-control-fixed <?= isset($errors['description'])?'error':'' ?>" maxlength="500" data-rules="required,min:30" data-label="La description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          <div style="display:flex;justify-content:space-between;margin-top:5px">
            <div class="form-error" id="e_description"><?= $errors['description'] ?? '' ?></div>
            <span id="charCount" style="font-size:.68rem;color:var(--muted);margin-left:auto">0 / 500</span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-check">
            <input type="checkbox" name="cgu" <?= isset($_POST['cgu']) ? 'checked' : '' ?>>
            <span class="form-check-text">
              J accepte les <a href="<?= $BASE ?>/legal/cgu.php" target="_blank">conditions generales</a> et la
              <a href="<?= $BASE ?>/legal/privacy.php" target="_blank">politique de confidentialite</a>.
            </span>
          </label>
          <div class="form-error"><?= $errors['cgu'] ?? '' ?></div>
        </div>

        <div class="form-actions">
          <a href="<?= $BASE ?>/inscription/step3.php" class="btn btn-ghost">← Retour</a>
          <button type="submit" class="btn btn-primary">Valider l inscription</button>
        </div>
      </form>
    </div>
  </div></div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function(){
  initUpload('photoInput','photoPreview','uploadZone');
  initUpload('carteInput','cartePreview','uploadZoneCI');
  initCharCount('description','charCount',500);
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
