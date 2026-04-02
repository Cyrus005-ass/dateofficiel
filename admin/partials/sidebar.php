<?php
$cur  = basename($_SERVER['PHP_SELF']);

?>
<aside class="admin-sidebar">
  <div class="sidebar-logo">
    <img src="<?= $BASE ?>/assets/img/logo.png" alt="Logo" style="max-height:38px;object-fit:contain" onerror="this.style.display='none'">
    <p style="color:var(--muted);font-size:.58rem;letter-spacing:2px;margin-top:6px">Administration</p>
  </div>
  <nav class="sidebar-nav">
    <a href="<?= $BASE ?>/admin/dashboard.php" class="<?= $cur==='dashboard.php'?'active':'' ?>">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>
    <a href="<?= $BASE ?>/admin/participants.php" class="<?= $cur==='participants.php'?'active':'' ?>">
      <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Participants
    </a>
    <a href="<?= $BASE ?>/admin/select.php" class="<?= $cur==='select.php'?'active':'' ?>">
      <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      Sélections
    </a>
    <a href="<?= $BASE ?>/admin/admins.php" class="<?= $cur==='admins.php'?'active':'' ?>">
      <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Administrateurs
    </a>
  </nav>
  <div class="sidebar-foot">
    <div class="sidebar-user">
      <strong><?= htmlspecialchars($_SESSION['admin_nom'] ?? 'Admin') ?></strong>
      <span><?= ucfirst($_SESSION['admin_role'] ?? 'admin') ?></span>
    </div>
    <a href="<?= $BASE ?>/admin/logout.php" class="sidebar-logout">← Déconnexion</a>
  </div>
</aside>
