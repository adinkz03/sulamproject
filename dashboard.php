<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — SulamProject</title>
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
  </head>
  <body>
    <div class="dashboard">
      <aside class="sidebar">
        <div class="brand">OurMasjid</div>
        <nav class="nav">
          <a href="dashboard.php">Dashboard</a>
          <a href="residents.php">Residents</a>
          <a href="donations.php">Donations</a>
          <a href="events.php">Events</a>
          <a href="logout.php">Logout</a>
        </nav>
      </aside>

      <main class="content">
        <div class="small-card" style="max-width:980px;margin:0 auto;padding:1.2rem 1.4rem;">
          <div class="dashboard-header">
            <h2 style="margin:0">Welcome</h2>
            <div>Hi, <strong><?php echo $username; ?></strong></div>
          </div>

          <section class="dashboard-cards">
            <a class="dashboard-card" href="residents.php">
              <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6Z"/></svg>
              </span>
              <h3>Residents</h3>
              <p>Manage residents and households.</p>
            </a>
            <a class="dashboard-card" href="donations.php">
              <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l2.1 4.3 4.7.7-3.4 3.3.8 4.7L12 14.8 7.8 17l.8-4.7L5.2 8l4.7-.7L12 3Zm-7 16h14v2H5v-2Z"/></svg>
              </span>
              <h3>Donations</h3>
              <p>Track donations and receipts.</p>
            </a>
            <a class="dashboard-card" href="events.php">
              <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2Zm13 7H4v11h16V9Z"/></svg>
              </span>
              <h3>Events</h3>
              <p>Plan and manage events.</p>
            </a>
          </section>
        </div>
      </main>
    </div>
  </body>
</html>
