<?php
$pageTitle = "Shopping Date — L'émission des célibataires | Muse Origin Studio et Elite Event Agency";
require_once __DIR__ . '/includes/header.php';
?>

<!-- ════════════════ HERO ════════════════ -->
<section class="hero" style="position:relative;min-height:100svh;display:flex;align-items:center;overflow:hidden;background:var(--black)">

  <!-- Vidéo fond -->
  <div style="position:absolute;inset:0;overflow:hidden;z-index:0">
    <video autoplay muted loop playsinline preload="auto"
           style="width:100%;height:100%;object-fit:cover;opacity:.22;filter:saturate(1.4)">
      <source src="<?= $BASE ?>/assets/img/anime.mp4" type="video/mp4">
    </video>
    <!-- Overlays -->
    <div style="position:absolute;inset:0;background:linear-gradient(to right,rgba(7,7,7,.97) 35%,rgba(7,7,7,.3) 100%)"></div>
    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(7,7,7,.95) 0%,transparent 45%)"></div>
  </div>

  <!-- Grille -->
  <div style="position:absolute;inset:0;z-index:1;background-image:linear-gradient(rgba(255,30,158,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,30,158,.025) 1px,transparent 1px);background-size:55px 55px"></div>

  <!-- Scanline -->
  <div id="scanline" style="position:absolute;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,rgba(255,30,158,.4),rgba(240,87,255,.4),transparent);z-index:2;pointer-events:none;top:0"></div>

  <!-- Radial glow -->
  <div style="position:absolute;inset:0;z-index:1;background:radial-gradient(ellipse at 15% 50%,rgba(255,30,158,.1) 0%,transparent 55%),radial-gradient(ellipse at 85% 25%,rgba(240,87,255,.06) 0%,transparent 40%)"></div>

  <!-- Particules -->
  <div id="particles" style="position:absolute;inset:0;z-index:2;pointer-events:none;overflow:hidden"></div>

  <!-- Orbite logo (desktop) -->
  <div class="hero-orbit" style="position:absolute;right:4%;top:50%;transform:translateY(-50%);width:clamp(280px,35vw,430px);height:clamp(280px,35vw,430px);display:flex;align-items:center;justify-content:center;z-index:5">
    <div style="position:absolute;inset:0;border-radius:50%;border:1px solid rgba(255,30,158,.12);animation:spin 28s linear infinite"></div>
    <div style="position:absolute;inset:clamp(20px,5%,40px);border-radius:50%;border:1px dashed rgba(240,87,255,.09);animation:spinRev 38s linear infinite"></div>
    <div style="position:absolute;inset:clamp(40px,10%,80px);border-radius:50%;border:1px solid rgba(255,10,16,.07);animation:spin 20s linear infinite"></div>
    <!-- Points orbitaux -->
    <div style="position:absolute;top:50%;left:50%;width:10px;height:10px;margin:-5px 0 0 -5px;border-radius:50%;background:var(--pink);box-shadow:0 0 16px var(--pink);animation:orbit 28s linear infinite"></div>
    <div style="position:absolute;top:50%;left:50%;width:7px;height:7px;margin:-3.5px 0 0 -3.5px;border-radius:50%;background:var(--purple);box-shadow:0 0 12px var(--purple);animation:orbitRev 38s linear infinite"></div>
    <!-- Glow central -->
    <div style="position:absolute;inset:20%;border-radius:50%;background:radial-gradient(circle,rgba(255,30,158,.14) 0%,transparent 70%);animation:glowPulse 4s ease-in-out infinite"></div>
    <!-- Logo -->
    <div style="position:relative;z-index:2;animation:float 6s ease-in-out infinite;padding:20px">
      <img src="<?= $BASE ?>/assets/img/logo.png" alt="Shopping Date"
           style="max-width:clamp(160px,22vw,250px);filter:drop-shadow(0 0 40px rgba(255,30,158,.5))"
           onerror="this.outerHTML='<div style=\'font-family:Bebas Neue,cursive;font-size:clamp(2rem,5vw,3.5rem);letter-spacing:5px;text-align:center;line-height:1;background:linear-gradient(135deg,#FF1E9E,#F057FF,#FF0A10);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text\'>THE<br>SH♥PPING<br>DATE</div>'">
    </div>
  </div>

  <!-- Contenu -->
  <div class="container" style="position:relative;z-index:10;padding-top:clamp(100px,15vw,130px);padding-bottom:clamp(60px,8vw,90px)">
    <div style="max-width:min(680px,90vw)">

      <!-- Eyebrow animé -->
      <div style="display:inline-flex;align-items:center;gap:10px;background:rgba(255,30,158,.1);border:1px solid rgba(255,30,158,.32);color:var(--pink);padding:6px 18px;border-radius:999px;font-size:.66rem;font-weight:700;letter-spacing:3px;text-transform:uppercase;margin-bottom:24px;animation:fadeDown .9s var(--ease) forwards;opacity:0;animation-delay:.2s">
        <span style="width:6px;height:6px;background:var(--pink);border-radius:50%;animation:pulse 1.8s ease-in-out infinite;box-shadow:0 0 8px var(--pink)"></span>
        Saison 1 &nbsp;·&nbsp; Inscriptions ouvertes
      </div>

      <!-- Titre principal -->
      <h1 style="font-family:var(--f-head);font-size:clamp(5.5rem,18vw,12rem);line-height:.82;letter-spacing:clamp(2px,1vw,6px);margin-bottom:22px">
        <span class="hero-line" style="display:block;color:var(--white);animation:fadeLeft .9s var(--ease) forwards;opacity:0;animation-delay:.35s">THE</span>
        <span class="hero-line" style="display:block;background:var(--g);background-size:200%;animation:gradFlow 5s ease infinite,fadeLeft .9s var(--ease) forwards;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;opacity:0;animation-delay:.5s">SH&#9829;PPING</span>
        <span class="hero-line" style="display:block;color:var(--white);animation:fadeLeft .9s var(--ease) forwards;opacity:0;animation-delay:.65s">DATE</span>
      </h1>

      <!-- Sous-titre -->
      <p style="font-size:clamp(.88rem,2.2vw,1rem);color:var(--muted);line-height:1.85;max-width:520px;margin-bottom:34px;animation:fadeUp .9s var(--ease) forwards;opacity:0;animation-delay:.8s">
        Deux célibataires. Un budget de 10 000 FCFA pour préparer le shopping et le rendez-vous.
        L'homme prend en charge le restaurant et la femme apporte un cadeau dans cette enveloppe.
      </p>

      <!-- CTA -->
      <div style="display:flex;gap:14px;flex-wrap:wrap;animation:fadeUp .9s var(--ease) forwards;opacity:0;animation-delay:.95s">
        <a href="<?= $BASE ?>/inscription/step1.php" class="btn btn-primary btn-lg">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          Je m'inscris — Gratuit
        </a>
        <a href="#concept" class="btn btn-outline btn-lg">Découvrir ↓</a>
        <a href="<?= htmlspecialchars($SITE_LINKS['season1']) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-lg">Saison 1 Bientôt disponible</a>
      </div>

      <!-- Stats -->
      <div style="display:flex;gap:clamp(24px,5vw,48px);margin-top:44px;padding-top:36px;border-top:1px solid rgba(255,255,255,.06);flex-wrap:wrap;animation:fadeUp .9s var(--ease) forwards;opacity:0;animation-delay:1.1s">
        <?php foreach ([['10000','FCFA','Budget offert'],['100','%','Inscription gratuite'],['S01','','Saison en cours']] as $s): ?>
        <div>
          <div style="font-family:var(--f-head);font-size:clamp(1.8rem,5vw,2.8rem);letter-spacing:3px;line-height:1;background:var(--g);background-size:200%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:gradFlow 5s ease infinite" data-count="<?= is_numeric($s[0])?$s[0]:'' ?>" data-suffix="<?= $s[1] ?>"><?= $s[0].($s[1]?' '.$s[1]:'') ?></div>
          <div style="font-size:.62rem;color:var(--gray);text-transform:uppercase;letter-spacing:2px;font-weight:600;margin-top:4px"><?= $s[2] ?></div>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>

  <!-- Scroll indicator -->
  <div style="position:absolute;bottom:28px;left:50%;transform:translateX(-50%);z-index:10;display:flex;flex-direction:column;align-items:center;gap:8px;animation:fadeIn 1.5s ease forwards 1.5s;opacity:0">
    <span style="font-size:.6rem;letter-spacing:3px;text-transform:uppercase;color:var(--gray)">Scroll</span>
    <div style="width:1px;height:40px;background:linear-gradient(to bottom,var(--pink),transparent);animation:fadeUp 1.5s ease-in-out infinite"></div>
  </div>
</section>

<!-- ════════════════ CONCEPT ════════════════ -->
<section class="section section-dark" id="concept">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(40px,8vw,90px);align-items:center">

      <!-- Média -->
      <div class="sr-left">
        <a href="<?= htmlspecialchars($SITE_LINKS['episode0']) ?>" target="_blank" rel="noopener" style="position:relative;border-radius:var(--r-xl);overflow:hidden;display:block">
          <!-- Bordure animée -->
          <div style="position:absolute;inset:-2px;border-radius:calc(var(--r-xl) + 2px);background:linear-gradient(135deg,#FF1E9E,#F057FF,#FF0A10,#FF1E9E);background-size:300% 300%;animation:gradFlow 4s ease infinite;z-index:-1"></div>
          <div style="position:relative;width:100%;max-height:520px;aspect-ratio:16/9;border-radius:var(--r-xl);overflow:hidden;background:#000">
            <iframe
              src="https://www.youtube-nocookie.com/embed/c4IUUvX_q4Y?rel=0&modestbranding=1"
              title="Episode 0 Shopping Date"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
              style="position:absolute;inset:0;width:100%;height:100%;border:0;display:block"
            ></iframe>
          </div>
          <div style="position:absolute;bottom:20px;left:20px;background:rgba(7,7,7,.88);backdrop-filter:blur(12px);border:1px solid rgba(255,30,158,.3);border-radius:999px;padding:8px 18px;display:flex;align-items:center;gap:9px;font-size:.76rem;font-weight:600">
            Episode 0 disponible sur YouTube
          </div>
        </a>
      </div>

      <!-- Texte -->
      <div class="sr-right">
        <div class="section-eyebrow"><span class="eyebrow-dot"></span>Le Concept</div>
        <h2 class="section-title">
          L'amour commence<br>
          <span class="text-grad">par le style</span>
        </h2>
        <div class="bar"></div>
        <p style="color:var(--light);font-size:clamp(.84rem,1.8vw,.94rem);line-height:1.85;margin-bottom:16px">
          Shopping Date est une emission porte par <strong>Muse Origin Studio</strong> et <strong>Elite Event Agency</strong>.
          Une enveloppe de <strong style="color:var(--pink)">10 000 FCFA</strong> couvre le shopping et le rendez-vous.
        </p>
        <p style="color:var(--muted);font-size:clamp(.82rem,1.8vw,.9rem);line-height:1.85;margin-bottom:28px">
          Le candidat organise sa préparation, l'homme paie le restaurant et la femme apporte un cadeau à l'homme,
          pour une premiere rencontre filmée dans un cadre romantique.
        </p>

        <?php foreach ([
          ['Budget dedie au shopping et au rendez-vous'],
          ['Production assuree par Muse Origin Studio & Elite Event Agency'],
          ['L homme paie le restaurant'],
          ['La femme apporte un cadeau dans les 10 000 FCFA'],
        ] as $f): ?>
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px">
          <span class="feature-line"></span>
          <span style="color:var(--light);font-size:.84rem"><?= $f[0] ?></span>
        </div>
        <?php endforeach; ?>

        <div style="margin-top:30px">
          <a href="<?= $BASE ?>/inscription/step1.php" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            Participer maintenant
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ════════════════ STEPS ════════════════ -->
<section class="section" id="steps">
  <div class="container">
    <div style="text-align:center" class="sr">
      <div class="section-eyebrow"><span class="eyebrow-dot"></span>Comment ça marche</div>
      <h2 class="section-title" style="margin-top:10px">4 étapes vers <span class="text-grad">le grand soir</span></h2>
      <div class="bar center"></div>
      <p class="section-sub center">Simple. Authentique. Inoubliable.</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(min(240px,100%),1fr));gap:20px">
      <?php
      $steps = [
        ['01','Inscrivez-vous','Formulaire en 3 étapes. Profil, photos, présentation. Totalement gratuit.','d1'],
        ['02','Sélection','Notre équipe choisit les profils les plus compatibles. Vous êtes notifié par email.','d2'],
        ['03','Preferences','Partagez vos preferences, vos attentes, vos red flags, vos green flags et votre vision du rendez-vous ideal.','d3'],
        ['04','Validation','Ajoutez votre photo, votre carte et finalisez votre candidature.','d4'],
      ];
      foreach ($steps as $s): ?>
      <div class="sr <?= $s[3] ?>" style="background:var(--dark1);border:1px solid rgba(255,255,255,.05);border-radius:var(--r-xl);padding:clamp(24px,4vw,36px);position:relative;overflow:hidden;transition:transform .4s var(--ease),border-color .3s,box-shadow .4s" onmouseover="this.style.transform='translateY(-8px)';this.style.borderColor='rgba(255,30,158,.22)';this.style.boxShadow='0 30px 70px rgba(0,0,0,.6)'" onmouseout="this.style.transform='';this.style.borderColor='';this.style.boxShadow=''">
        <div style="font-family:var(--f-head);font-size:4.5rem;letter-spacing:2px;background:var(--g);background-size:200%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:gradFlow 6s ease infinite;opacity:.22;line-height:1;margin-bottom:14px"><?= $s[0] ?></div>
        <h3 style="font-family:var(--f-serif);font-size:1.1rem;font-weight:700;margin-bottom:10px;color:var(--white)"><?= $s[1] ?></h3>
        <p style="color:var(--muted);font-size:.86rem;line-height:1.78"><?= $s[2] ?></p>
        <!-- Barre top hover -->
        <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--g);background-size:200%;animation:gradFlow 4s ease infinite;transform:scaleX(0);transform-origin:left;transition:transform .45s var(--ease)" class="step-bar"></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ════════════════ FAQ ════════════════ -->
<section class="section section-dark" id="faq">
  <div class="container">
    <div style="text-align:center" class="sr">
      <div class="section-eyebrow"><span class="eyebrow-dot"></span>Questions frequentes</div>
      <h2 class="section-title" style="margin-top:10px">FAQ <span class="text-grad">Shopping Date</span></h2>
      <div class="bar center"></div>
      <p class="section-sub center">Retrouvez rapidement les informations essentielles avant votre inscription.</p>
    </div>

    <div class="faq-list">
      <?php foreach ([
        ['Qui peut s\'inscrire ?', 'Les candidatures sont ouvertes aux personnes agees de 18 a 45 ans, celibataires et disponibles pour le tournage.'],
        ['L\'inscription est-elle payante ?', 'Non. L\'inscription a Shopping Date est totalement gratuite du debut a la fin du processus.'],
        ['Comment se passe la selection ?', 'Notre equipe étudie chaque dossier puis contacte uniquement les profils retenus pour la suite.'],
        ['A quoi servent les 10 000 FCFA ?', 'Cette enveloppe sert au shopping et au rendez-vous, avec le restaurant pris en charge par l\'homme et le cadeau apporte par la femme.'],
      ] as $i => $faq): ?>
      <details class="faq-item sr d<?= ($i % 4) + 1 ?>">
        <summary><?= $faq[0] ?></summary>
        <p><?= $faq[1] ?></p>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ════════════════ CTA BAND ════════════════ -->
<section style="padding:clamp(80px,12vw,130px) 0;background:var(--dark1);text-align:center;position:relative;overflow:hidden">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse at center,rgba(255,30,158,.1) 0%,transparent 65%)"></div>
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,30,158,.018) 1px,transparent 1px),linear-gradient(90deg,rgba(255,30,158,.018) 1px,transparent 1px);background-size:55px 55px"></div>
  <div class="container" style="position:relative;z-index:2">
    <div class="sr">
      <div class="section-eyebrow"><span class="eyebrow-dot"></span>Prêt(e) ?</div>
      <h2 style="font-family:var(--f-head);font-size:clamp(3rem,8vw,7rem);letter-spacing:4px;line-height:.9;margin:16px 0 22px">
        Vivez l'expérience<br>
        <span class="text-shimmer">Shopping Date</span>
      </h2>
      <div style="display:inline-flex;align-items:center;gap:10px;background:rgba(255,30,158,.08);border:1px solid rgba(255,30,158,.25);border-radius:999px;padding:10px 22px;margin-bottom:30px;font-size:.8rem;color:var(--light)">
        Budget global : <strong style="color:var(--pink);font-size:.94rem">10 000 FCFA pour shopping et date</strong>
      </div>
      <p style="color:var(--gray);font-size:.82rem;margin-bottom:32px">Inscription gratuite · 18–45 ans · Places limitees</p>
      <a href="<?= $BASE ?>/inscription/step1.php" class="btn btn-primary btn-lg" style="animation:glowPulse 3s ease-in-out infinite">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        S'inscrire gratuitement
      </a>
    </div>
  </div>
</section>

<!-- ════════════════ STUDIO ════════════════ -->
<section class="section" id="studio">
  <div class="container">
      <div class="sr studio-intro">
        <div class="section-eyebrow"><span class="eyebrow-dot"></span>Muse Origin Studio & Elite Event Agency</div>
        <h2 class="section-title" style="margin-top:10px">Production et diffusion</h2>
        <div class="bar"></div>
        <p style="color:var(--light);font-size:clamp(.84rem,1.8vw,.94rem);line-height:1.85;margin-bottom:16px">Muse Origin Studio & Elite Event Agency accompagnent Shopping Date sur la production et la co-production du projet, avec une diffusion assurée par Leben Entertainment.</p>
        <div class="team-block">
          <br> <br>
          <h3 class="team-block-title">Direction du projet</h3>
          <div class="team-inline-grid team-inline-grid-direction">
            <?php foreach ($PROJECT_DIRECTION as $member): ?>
            <div class="tech-team-card">
              <strong><?= htmlspecialchars($member['name']) ?></strong>
              <span><?= htmlspecialchars($member['role']) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <div class="partner-sections">
      <?php foreach ($SITE_PARTNERS as $index => $partner): ?>
      <section class="partner-card partner-card-feature <?= $index % 2 === 1 ? 'partner-card-feature-reverse' : '' ?> partner-card-<?= htmlspecialchars($partner['id']) ?> sr">
        <div class="partner-copy partner-copy-feature">
          <div class="partner-role"><?= htmlspecialchars($partner['role']) ?></div>
          <h3 class="partner-heading"><?= htmlspecialchars($partner['name']) ?></h3>
          <p class="partner-text"><?= htmlspecialchars($partner['tagline']) ?></p>
          <?php if (!empty($partner['description'])): ?>
          <p class="partner-text"><?= htmlspecialchars($partner['description']) ?></p>
          <?php endif; ?>
        </div>
        <div class="partner-media">
          <img src="<?= htmlspecialchars($partner['logo']) ?>" alt="<?= htmlspecialchars($partner['name']) ?>"
               class="partner-logo partner-logo-<?= htmlspecialchars($partner['id']) ?>"
               onerror="this.outerHTML='<div class=\'brand-fallback\'><?= htmlspecialchars(strtoupper($partner['name'])) ?></div>'">
        </div>
        <div class="partner-socials social-icons">
          <?php foreach ($partner['socials'] as $social): ?>
          <?php if ($social['url'] !== '#'): ?>
          <a href="<?= htmlspecialchars($social['url']) ?>" class="partner-social-icon" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($social['label']) ?>">
            <?php if ($social['label'] === 'Instagram'): ?>
            <i class="fab fa-instagram social-icon"></i>
            <?php elseif ($social['label'] === 'TikTok'): ?>
            <i class="fab fa-tiktok social-icon"></i>
            <?php elseif ($social['label'] === 'Facebook'): ?>
            <i class="fab fa-facebook social-icon"></i>
            <?php elseif ($social['label'] === 'YouTube'): ?>
            <i class="fab fa-youtube social-icon"></i>
            <?php elseif ($social['label'] === 'WhatsApp'): ?>
            <i class="fab fa-whatsapp social-icon"></i>
            <?php endif; ?>
          </a>
          <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script>
// Hover step bar (CSS-only fallback for older browsers)
document.querySelectorAll('.sr[class*="d"]').forEach(function(card){
  var bar = card.querySelector('.step-bar');
  if(!bar) return;
  card.addEventListener('mouseenter',function(){ bar.style.transform='scaleX(1)' });
  card.addEventListener('mouseleave',function(){ bar.style.transform='scaleX(0)' });
});
</script>

<!-- Responsive hero orbit -->
<style>
@media(max-width:900px){ .hero-orbit{display:none!important} }
@media(max-width:768px){
  .hero h1 span[style*="font-size"]{font-size:clamp(4.5rem,20vw,7rem)!important}
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
