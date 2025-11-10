<?php
// Login handler: verifies username/password against DB and redirects to dashboard
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once __DIR__ . '/db.php';

  $rawUsername = isset($_POST['username']) ? trim($_POST['username']) : '';
  $rawPassword = isset($_POST['password']) ? $_POST['password'] : '';

  if ($rawUsername === '' || $rawPassword === '') {
    $message = 'Please enter your username and password.';
  } else {
    // Fetch user by username
    $stmt = $mysqli->prepare('SELECT id, username, email, password_hash FROM `users` WHERE username = ? LIMIT 1');
    if ($stmt) {
      $stmt->bind_param('s', $rawUsername);
      $stmt->execute();
      $result = $stmt->get_result();
      $user = $result ? $result->fetch_assoc() : null;
      $stmt->close();

      if ($user && password_verify($rawPassword, $user['password_hash'])) {
        // Success: set session and redirect
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
      } else {
        $message = 'Invalid username or password.';
      }
    } else {
      $message = 'Login failed. Please try again later.';
    }
  }
}
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — SulamProject</title>
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
  </head>
  <body>
    <main class="centered small-card">
      <h2>Login</h2>
      <?php if ($message): ?><div class="notice"><?php echo $message; ?></div><?php endif; ?>

      <form method="post" action="login.php">
        <label>Username
          <input type="text" name="username" required>
        </label>
        <label>Password
          <input type="password" name="password" required>
        </label>
        <div class="actions">
          <button class="btn" type="submit">Sign in</button>
          <a class="btn outline" href="register.php">Register</a>
        </div>
      </form>
    </main>
  </body>
</html>
