<?php
// $lang, $L should be set in the parent file
if (!isset($lang) || !isset($L)) {
    require_once __DIR__."/eng_sw_lang.php";
    $lang = $_SESSION['lang'] ?? "en";
    $L = $LANGUAGES[$lang];
}
$user = $_SESSION['user'] ?? null;
$isAdmin = $user && (isset($user['role']) && $user['role'] === 'admin');
$username = $user ? htmlspecialchars($user['name']) : '';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="sf-nav-tour">
  <div class="sf-nav-tour__brand">
    <a href="index.php" class="sf-nav-tour__brand-link">
      <img src="assets/img/dashboard.jpg" alt="SmartFarm Logo" class="sf-nav-tour__logo">
      <span class="sf-nav-tour__title"><?= $L['short_title'] ?? 'SmartFarm' ?></span>
    </a>
    <div class="sf-nav-tour__user-and-hamburger">
      <?php if($user): ?>
        <div class="sf-nav-tour__user">
          <i class="bi bi-person-circle"></i>
          <span class="sf-nav-tour__user-name"><?= $username ?></span>
          <?php if($isAdmin): ?><span class="sf-nav-tour__admin-badge">admin</span><?php endif; ?>
        </div>
        
        
      <?php endif; ?>
      <button class="sf-nav-tour__hamburger" id="sfNavbarToggle" aria-label="Open menu" aria-controls="sfNavbarOverlay" aria-expanded="false">
        <span class="sf-nav-tour__hamburger-bar"></span>
        <span class="sf-nav-tour__hamburger-bar"></span>
        <span class="sf-nav-tour__hamburger-bar"></span>
      </button>
    </div>
  </div>
</nav>

<!-- Overlay Menu -->
<div class="sf-nav-tour__overlay" id="sfNavbarOverlay">
  <div class="sf-nav-tour__overlay-header">
    <a href="index.php" class="sf-nav-tour__brand-link">
      <img src="assets/img/dashboard.jpg" alt="SmartFarm Logo" class="sf-nav-tour__logo">
      <span class="sf-nav-tour__title"><?= $L['short_title'] ?? 'SmartFarm' ?></span>
    </a>
    <button class="sf-nav-tour__close-btn" id="sfCloseNavBtn" aria-label="Close menu">
      <span class="sf-nav-tour__close-x"></span>
    </button>
  </div>
  <ul class="sf-nav-tour__menu">
    <li><a href="index.php" class="<?= $currentPage=='index.php'?'active':'' ?>"><?= $L['home'] ?></a></li>
    <li><a href="dashboard.php" class="<?= $currentPage=='dashboard.php'?'active':'' ?>"><?= $L['dashboard'] ?></a></li>
    <li><a href="forum.php" class="<?= $currentPage=='forum.php'?'active':'' ?>"><?= $L['community'] ?></a></li>
    <?php if($isAdmin): ?>
      <li><a href="Admin/index.php" class="sf-nav-tour__menu-admin <?= strpos($_SERVER['PHP_SELF'],'/Admin/')!==false?'active':'' ?>">Admin Panel</a></li>
    <?php endif; ?>
    <?php if($user): ?>
      <li><a href="profile.php"><?= $L['profile'] ?? 'Profile' ?></a></li>
      <li><a href="logout.php" class="logout"><?= $L['logout'] ?></a></li>
    <?php endif; ?>
  </ul>
</div>

<style>
/* Import font */
@import url('https://fonts.googleapis.com/css?family=Inter:400,500,700&display=swap');
:root {
  --sf-nav-bg: #1d8261;
  --sf-nav-link: #0b0a0a;
  --sf-nav-link-active: #17b673;
  --sf-nav-divider: rgba(74, 222, 128, 0.10);
  --sf-gold: #e7b10a;
  --sf-admin-bg: #fffbe9;
  --sf-admin-color: #b28500;
  --sf-profile-bg: #13b67c19;
}

/* Nav Bar */
.sf-nav-tour {
  width: 100%;
  background: var(--sf-nav-bg);
  min-height: 60px;
  box-shadow: 0 2px 8px rgba(0,0,0,.1);
  position: sticky; top: 0; z-index: 110;
  font-family: 'Inter', Arial, sans-serif;
}

.sf-nav-tour__brand {
  max-width: 1130px;
  margin: 0 auto;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding: 0 18px;
}

.sf-nav-tour__brand-link {
  display: flex;
  align-items: center;
  gap: 13px;
  text-decoration: none;
}
.sf-nav-tour__logo {
  width: 38px; height: 38px;
  border-radius: 10px;
  background: #e7fdf7;
  object-fit: cover;
  border: 1.1px solid #1e7b58;
  box-shadow: 0 2px 8px #05966911;
}
.sf-nav-tour__title {
  color: #fff;
  font-weight: 900;
  font-size: 1.21rem;
  letter-spacing: 0.07em;
  text-shadow: 0 1px 3px #05966966;
  text-transform: uppercase;
}

.sf-nav-tour__user-and-hamburger {
  display: flex;
  align-items: center;
  gap: 7px;
}

.sf-nav-tour__user {
  color: #f0ffe3;
  font-weight: 600;
  font-size: 1em;
  background: transparent;
  display: flex;
  align-items: center;
  gap: 0.21em;
  padding: 0.13em .77em 0.13em 0.6em;
  border-radius: 16px;
  border: none;
}
.sf-nav-tour__user-name {
  margin-left:.2em;
}
.sf-nav-tour__admin-badge {
  display:inline-block;
  background: var(--sf-admin-bg);
  color: var(--sf-admin-color);
  font-size: .92em;
  font-weight: 700;
  margin-left:.4em;
  padding: 0 .6em;
  border-radius: .5em;
  text-transform: lowercase;
  letter-spacing:0.01em;
  box-shadow:0 1px 4px #e7b10a33;
}
.sf-nav-tour__profile-link {
  display:inline-block;
  margin-left: 7px;
  color: #fff;
  background: var(--sf-profile-bg);
  border-radius: 11px;
  font-size: 1em;
  font-weight:500;
  padding: 0.3em 1.12em;
  text-decoration: none;
  border: none;
  transition: background .18s, color .18s;
}
.sf-nav-tour__profile-link:hover,
.sf-nav-tour__profile-link:focus {
  background: #fff;
  color: #198754;
}
.sf-nav-tour__logout {
  display:inline-block;
  color: #ff5757;
  background: none;
  border-radius: 11px;
  padding: 0.3em 1.12em;
  font-size: 1em;
  font-weight: 600;
  text-decoration: none;
  border: none;
  margin-left: 7px;
  transition: background .15s, color .16s;
}
.sf-nav-tour__logout:hover,
.sf-nav-tour__logout:focus {
  background: #fee;
  color: #b22222;
}

.sf-nav-tour__hamburger {
  display: flex; flex-direction: column; gap: 7px;
  height: 27px; width: 44px;
  background: none; border: none;
  cursor: pointer;
  margin-left: 8px;
  padding: 0;
  z-index: 12000;
  border-radius: 50%;
  transition: background 0.17s;
}
.sf-nav-tour__hamburger:hover, .sf-nav-tour__hamburger:focus {
  background: #094231;
}

.sf-nav-tour__hamburger-bar {
  width: 28px; height: 3px; background: #fff;
  border-radius: 2.5px; display: block; transition: background 0.18s;
}

/* Overlay slide-in styles */
.sf-nav-tour__overlay {
  display: none;
  position: fixed;
  top: 0;
  right: 0;
  width: min(97vw, 390px);
  height: 100vh;
  background: var(--sf-nav-bg);
  box-shadow: -2px 0 18px rgba(0,0,0,.21);
  z-index: 25000;
  flex-direction: column;
  animation: slideInNav 0.25s cubic-bezier(0.55,0,0.6, 1);
}
@keyframes slideInNav {
  from { right: -420px; opacity:0;}
  to { right: 0; opacity: 1;}
}
.sf-nav-tour__overlay.show { display: flex; }

.sf-nav-tour__overlay-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 72px;
  box-sizing: border-box;
  border-bottom: 1px solid var(--sf-nav-divider);
  padding: 0 21px 0 17px;
  width: 100%;
}
.sf-nav-tour__close-btn {
  background: none;
  border: none;
  height: 38px; width: 38px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; z-index: 25100;
  transition: background .17s;
  border-radius: 50%;
}
.sf-nav-tour__close-btn:hover .sf-nav-tour__close-x::before,
.sf-nav-tour__close-btn:hover .sf-nav-tour__close-x::after {
  background: var(--sf-gold);
}
.sf-nav-tour__close-x {
  display: block;
  position: relative;
  width: 29px; height: 29px;
}
.sf-nav-tour__close-x::before,
.sf-nav-tour__close-x::after {
  content: '';
  position: absolute;
  left: 13.5px; top: 1px;
  width: 2.3px; height: 26px;
  background: #dfdfdf;
  border-radius: 2px;
  transition: all 0.17s;
}
.sf-nav-tour__close-x::before { transform: rotate(45deg);}
.sf-nav-tour__close-x::after { transform: rotate(-45deg);}

.sf-nav-tour__menu {
  list-style: none;
  margin: 0; margin-top: 17px;
  padding: 0 24px;
  display: flex;
  flex-direction: column;
  width: 100%;
  gap: 0;
  overflow-y: auto;
  max-height: calc(100vh - 72px);
  background-clip: padding-box;
}
.sf-nav-tour__menu li { width: 100%; margin: 0; padding: 0;}
.sf-nav-tour__menu li:not(:last-child) { border-bottom: 1px solid var(--sf-nav-divider);}
.sf-nav-tour__menu li a,
.sf-nav-tour__menu li a:visited {
  display: block;
  color: var(--sf-nav-link);
  font-family: 'Inter', Arial, sans-serif;
  font-size: 1.13rem;
  font-weight: 700;
  letter-spacing: 0.11em;
  text-transform: uppercase;
  padding: 17px 0 14px 0;
  text-decoration: none;
  transition: background .18s, color .15s;
  position: relative;
  border-radius: .8em;
}
.sf-nav-tour__menu li a:hover,
.sf-nav-tour__menu li a.active,
.sf-nav-tour__menu li a:focus {
  color: var(--sf-nav-link-active);
  background: #093f35;
}

.sf-nav-tour__menu-admin {
  color: var(--sf-gold)!important; font-weight: 800;
}
.sf-nav-tour__menu-admin.active,
.sf-nav-tour__menu-admin:hover,
.sf-nav-tour__menu-admin:focus {
  background: #1e2722; color: #fff7c8!important;
}

.sf-nav-tour__menu .logout {
  color: #ff5757;
  font-weight: 900;
}
.sf-nav-tour__menu .logout:hover {
  background: #420c0c; color: #fff;
}

@media (max-width: 570px) {
  .sf-nav-tour__overlay { width: 99vw; }
  .sf-nav-tour__menu { padding: 0 8px; max-height: calc(100vh - 64px);}
  .sf-nav-tour__overlay-header { min-height: 64px; padding:0 8px 0 5px;}
  .sf-nav-tour__title { font-size:.97rem;}
}
@media (max-width: 420px) {
  .sf-nav-tour__brand { padding: 0 3px;}
  .sf-nav-tour__logo { width:27px; height:27px;}
  .sf-nav-tour__title { font-size:.86rem;}
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var toggler = document.getElementById('sfNavbarToggle');
  var overlay = document.getElementById('sfNavbarOverlay');
  var closeBtn = document.getElementById('sfCloseNavBtn');
  toggler.addEventListener('click', function (e) {
    e.stopPropagation();
    overlay.classList.add('show');
    toggler.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  });
  closeBtn.addEventListener('click', function () {
    overlay.classList.remove('show');
    toggler.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  });
  let navLinks = overlay.querySelectorAll("a");
  navLinks.forEach(function(link){
    link.addEventListener('click', function(){
      overlay.classList.remove('show');
      toggler.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    });
  });
  document.addEventListener('click', function(event) {
    if (overlay.classList.contains('show') && !overlay.contains(event.target) && event.target !== toggler) {
      overlay.classList.remove('show');
      toggler.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }
  });
  document.addEventListener("keydown", function(e){
    if(overlay.classList.contains('show') && (e.key === "Escape" || e.key === "Esc")) {
      overlay.classList.remove('show');
      toggler.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }
  });
});
</script>