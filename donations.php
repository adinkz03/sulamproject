<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donations — SulamProject</title>
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
        <div class="small-card" style="max-width:980px;margin:0 auto;">
          <h2>Donations</h2>
          <p>This section is coming soon.</p>
        </div>
      </main>
    </div>
  </body>
</html>
