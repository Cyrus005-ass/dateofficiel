<?php $BASE = $BASE ?? ''; ?>

<!-- ══ FOOTER ══ -->
<footer class="footer">
  <div class="container">
    <div class="footer-grid">

      <!-- Brand -->
      <div>
        <img src="<?= $BASE ?>/assets/img/logo.png" alt="Shopping Date" class="footer-logo"
             style="height:56px;width:auto;object-fit:contain"
             onerror="this.outerHTML='<div class=\'brand-fallback brand-fallback-footer\'>SHOPPING DATE</div>'">
        <p class="footer-desc">
          L'emission qui reunit des celibataires autour du shopping et d'un rendez-vous.
          Muse Origin Studio & Elite Event Agency accompagnent le projet.
        </p>
        <div class="socials social-icons">
          <a class="social-btn" href="<?= htmlspecialchars($SITE_LINKS['instagram']) ?>" aria-label="Instagram" target="_blank" rel="noopener">
            <i class="fab fa-instagram social-icon"></i>
          </a>
          <a class="social-btn" href="<?= htmlspecialchars($SITE_LINKS['tiktok']) ?>" aria-label="TikTok" target="_blank" rel="noopener">
            <i class="fab fa-tiktok social-icon"></i>
          </a>
          <a class="social-btn" href="<?= htmlspecialchars($SITE_LINKS['facebook']) ?>" aria-label="Facebook" target="_blank" rel="noopener">
            <i class="fab fa-facebook social-icon"></i>
          </a>
          <a class="social-btn" href="<?= htmlspecialchars($SITE_LINKS['youtube']) ?>" aria-label="YouTube" target="_blank" rel="noopener">
            <i class="fab fa-youtube social-icon"></i>
          </a>
          <a class="social-btn" href="mailto:<?= htmlspecialchars($SITE_LINKS['contact_email']) ?>" aria-label="Nous écrire par e-mail">
            <i class="fas fa-envelope social-icon"></i>
          </a>
        </div>
      </div>

      <!-- Navigation -->
      <div class="footer-col">
        <h4>Navigation</h4>
        <div class="footer-nav-links">
          <a href="<?= $BASE ?>/#concept">Le Concept</a>
          <a href="<?= $BASE ?>/#steps">Comment ça marche</a>
          <a href="<?= htmlspecialchars($SITE_LINKS['season1']) ?>" target="_blank" rel="noopener">Voir la saison 1</a>
          <a href="<?= $BASE ?>/#faq">FAQ</a>
          <a href="<?= $BASE ?>/#studio">Le Studio</a>
        </div>
      </div>

      <!-- Partenaires -->
      <div class="footer-col">
        <h4>Partenaires</h4>
        <div class="footer-partners-stack">
          <?php foreach ($SITE_PARTNERS as $partner): ?>
          <div class="footer-partner-card <?= $partner['website'] === '#' ? 'is-disabled' : '' ?>">
            <div class="footer-partner-role"><?= htmlspecialchars($partner['role']) ?></div>
            <img src="<?= htmlspecialchars($partner['logo']) ?>" alt="<?= htmlspecialchars($partner['name']) ?>" class="footer-muse-logo"
                 onerror="this.style.display='none'">
            <div class="footer-muse-name"><?= htmlspecialchars($partner['name']) ?></div>
            <div class="footer-muse-tag"><?= htmlspecialchars($partner['tagline']) ?></div>
            <div class="footer-partner-icons social-icons">
              <?php foreach ($partner['socials'] as $social): ?>
              <a href="<?= htmlspecialchars($social['url']) ?>" class="footer-partner-icon <?= $social['url'] === '#' ? 'is-disabled' : '' ?>" <?= $social['url'] !== '#' ? 'target="_blank" rel="noopener"' : 'aria-disabled="true"' ?> aria-label="<?= htmlspecialchars($social['label']) ?>">
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
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <div class="footer-tech-row">
      <h4>Prestataires</h4>
      <div class="footer-tech-stack">
        <?php foreach ($TECH_TEAM as $member): ?>
        <a href="<?= htmlspecialchars($member['linkedin'] ?? '#') ?>" class="footer-tech-card <?= ($member['linkedin'] ?? '#') === '#' ? 'is-disabled' : '' ?>" <?= ($member['linkedin'] ?? '#') !== '#' ? 'target="_blank" rel="noopener"' : 'aria-disabled="true"' ?>>
          <strong><?= htmlspecialchars($member['name']) ?></strong>
          <span><?= htmlspecialchars($member['role']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> Shopping Date &mdash; <a href="<?= $BASE ?>/admin/login.php" style="text-decoration: none;">Muse Origin Studio & Elite Event Agency</a>. Tous droits réservés.</p>
    </div>
  </div>
</footer>

<!-- ★ JS GLOBAL — inclus en bas de chaque page via footer ★ -->
<script src="<?= $BASE ?>/assets/js/main.js"></script>
<?php if (isset($extraFooter)) echo $extraFooter; ?>
</body>
</html>
