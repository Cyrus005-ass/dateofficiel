<?php
session_start();
$pageTitle = 'Inscription — Étape 1 | Shopping Date';

$data = $_SESSION['step1'] ?? [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../includes/db.php';
    $nom    = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $tel    = trim($_POST['telephone'] ?? '');

    if (strlen($nom) < 2)                         $errors['nom']       = 'Le nom doit contenir au moins 2 caractères.';
    if (strlen($prenom) < 2)                      $errors['prenom']    = 'Le prénom doit contenir au moins 2 caractères.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']  = 'Adresse email invalide.';
    if (!preg_match('/^[\d\s+\-()\/.]{7,20}$/', $tel)) $errors['telephone'] = 'Numéro de téléphone invalide.';

    if (empty($errors)) {
        $s = getPDO()->prepare("SELECT id FROM participants WHERE email=?");
        $s->execute([$email]);
        if ($s->fetch()) $errors['email'] = 'Cet email est déjà inscrit.';
    }

    if (empty($errors)) {
        $_SESSION['step1'] = compact('nom','prenom','email') + ['telephone' => $tel];
        header('Location: ' . $BASE . '/inscription/step2.php'); exit;
    }
    $data = compact('nom','prenom','email') + ['telephone' => $tel];
}
require_once __DIR__ . '/../includes/header.php';
?>
<main class="form-page">
  <div class="form-banner">
    <div class="container">
      <div class="section-eyebrow"><span class="eyebrow-dot"></span>Inscription gratuite</div>
      <h1>REJOINS L'AVENTURE</h1>
      <p>Remplis le formulaire pour participer a Shopping Date</p>
      <div class="progress">
        <div class="prog-step"><div class="prog-circle active">1</div><div class="prog-label active">Identité</div></div>
        <div class="prog-line"></div>
        <div class="prog-step"><div class="prog-circle">2</div><div class="prog-label">Profil</div></div>
        <div class="prog-line"></div>
        <div class="prog-step"><div class="prog-circle">3</div><div class="prog-label">Préférences</div></div>
        <div class="prog-line"></div>
        <div class="prog-step"><div class="prog-circle">4</div><div class="prog-label">Validation</div></div>
      </div>
    </div>
  </div>

  <div class="form-section">
    <div class="container">
      <div class="form-card sr">
        <h2 class="form-card-title">Étape 1 — <span class="text-grad">Vos coordonnées</span></h2>
        <p class="form-card-sub">Informations confidentielles et sécurisées.</p>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><span>Erreur</span> Veuillez corriger les erreurs ci-dessous.</div>
        <?php endif; ?>

        <form method="POST" id="sd-form" novalidate>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="nom">Nom <span>*</span></label>
              <input type="text" id="nom" name="nom" class="form-control <?= isset($errors['nom'])?'error':'' ?>"
                     placeholder="Votre nom" value="<?= htmlspecialchars($data['nom']??'') ?>"
                     data-rules="required,min:2" data-label="Le nom" autocomplete="family-name">
              <div class="form-error" id="e_nom"><?= $errors['nom']??'' ?></div>
            </div>
            <div class="form-group">
              <label class="form-label" for="prenom">Prénom <span>*</span></label>
              <input type="text" id="prenom" name="prenom" class="form-control <?= isset($errors['prenom'])?'error':'' ?>"
                     placeholder="Votre prénom" value="<?= htmlspecialchars($data['prenom']??'') ?>"
                     data-rules="required,min:2" data-label="Le prénom" autocomplete="given-name">
              <div class="form-error" id="e_prenom"><?= $errors['prenom']??'' ?></div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="email">Email <span>*</span></label>
            <input type="email" id="email" name="email" class="form-control <?= isset($errors['email'])?'error':'' ?>"
                   placeholder="votre@email.com" value="<?= htmlspecialchars($data['email']??'') ?>"
                   data-rules="required,email" data-label="L'email" autocomplete="email">
            <div class="form-error" id="e_email"><?= $errors['email']??'' ?></div>
          </div>
          <div class="form-group">
            <label class="form-label" for="telephone">Téléphone <span>*</span></label>
            <input type="tel" id="telephone" name="telephone" class="form-control <?= isset($errors['telephone'])?'error':'' ?>"
                   placeholder="+229 XX XX XX XX" value="<?= htmlspecialchars($data['telephone']??'') ?>"
                   data-rules="required,phone" data-label="Le téléphone" autocomplete="tel">
            <div class="form-error" id="e_telephone"><?= $errors['telephone']??'' ?></div>
          </div>
          <div class="form-actions">
            <a href="<?= $BASE ?>/" class="btn btn-ghost">← Retour</a>
            <button type="submit" class="btn btn-primary">Continuer → Étape 2</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
