<footer class="smart-footer mt-5">
  <div class="footer-container">
    <div class="footer-brand">
      <img src="assets/img/dashboard.jpg" alt="Smart Farm logo">
      <span class="footer-title">SmartFarm Tanzania</span>
    </div>
    <div class="footer-links">
      <a href="index.php">Home</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="learning_hub.php">Learning Hub</a>
      <a href="forum.php">Community</a>
      <a href="help.php">Help</a>
    </div>
  </div>
  <div class="footer-bottom">
    &copy; <?= date('Y') ?> SmartFarm Tanzania &mdash; Empowering Farmers. All rights reserved.
  </div>
  <style>
    .smart-footer {
      background: #059669;
      color: #fff;
      padding: 2.2em 0 0.7em 0;
      border-top: 2.5px solid #34d39955;
      box-shadow: 0 -2px 18px #04785720;
      font-family: 'Inter', Arial, sans-serif;
      font-size: 1.08em;
      margin-top: 2em;
      width:100%;
    }
    .footer-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1070px;
      margin: 0 auto;
      flex-wrap: wrap;
      gap: 0.7em;
    }
    .footer-brand {
      display: flex; align-items: center; gap: 0.7em;
    }
    .footer-brand img {
      width: 34px; height: 34px; border-radius: 8px; background: #fff;
      box-shadow: 0 1px 10px #05966938;
    }
    .footer-title {
      color: #fff;
      font-size: 1.15em;
      font-weight: bold;
      letter-spacing: 0.5px;
      text-shadow: 0 1px 6px #03744b99;
    }
    .footer-links {
      display: flex; gap: 1.3em; flex-wrap: wrap;
    }
    .footer-links a {
      color: #e0fcee;
      text-decoration: none;
      font-weight: 500;
      padding: 2px 11px;
      border-radius: 6px;
      transition: background .15s, color .13s;
      font-size: 1em;
      letter-spacing: .01em;
    }
    .footer-links a:hover {
      background: #34d399;
      color: #024c32;
      text-decoration: none;
    }
    .footer-bottom {
      text-align: center;
      color: #e0fcee;
      font-size: .99em;
      margin-top: 1.3em;
      padding-bottom:0.27em;
      letter-spacing: 0.03em;
      text-shadow: 0 1px 6px #04785730;
    }
    @media (max-width:670px) {
      .footer-container {
        flex-direction: column;
        gap: 0.9em 0;
        align-items: flex-start;
        padding-left:1em; padding-right:1em;
      }
      .footer-links {gap: 1em;}
    }
    @media (max-width:410px) {
      .footer-brand .footer-title {font-size:0.99em;}
      .footer-links {font-size:0.98em;}
    }
  </style>
</footer>