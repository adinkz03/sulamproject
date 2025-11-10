<?php
// Simple front page with links to login and register
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SulamProject — Welcome</title>
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
  </head>
  <body>
    <div class="page-card">
    <main class="centered">
      <h1>WELCOME TO OurMasjid</h1>
      <p class="lead">ONE STOP CENTRE FOR EVERYTHING</p>

      <div class="actions">
        <a class="btn" href="login.php">Login</a>
        <a class="btn outline" href="register.php">Register</a>
      </div>

      <footer class="small">Solatlah sebelum kamu disolatkan...</footer>
    </main>
    </div>
  </body>
</html>
