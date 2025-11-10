<?php
$message = '';
$messageClass = 'notice';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once __DIR__ . '/db.php'; // ensures DB and `users` table exist in `masjid`

  // Collect & validate inputs
  $rawUsername = isset($_POST['username']) ? trim($_POST['username']) : '';
  $rawEmail = isset($_POST['email']) ? trim($_POST['email']) : '';
  $rawPassword = isset($_POST['password']) ? $_POST['password'] : '';

  $errors = [];
  if ($rawUsername === '' || strlen($rawUsername) < 3 || strlen($rawUsername) > 50) {
    $errors[] = 'Username must be between 3 and 50 characters.';
  }
  if (!filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address.';
  } elseif (strlen($rawEmail) > 120) {
    $errors[] = 'Email must be at most 120 characters.';
  }
  if (strlen($rawPassword) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
  }

  if (empty($errors)) {
    $username = $mysqli->real_escape_string($rawUsername);
    $email = $mysqli->real_escape_string($rawEmail);
    $passwordHash = password_hash($rawPassword, PASSWORD_DEFAULT);

  // Use prepared statement for insert into masjid.users
  $stmt = $mysqli->prepare('INSERT INTO `users` (username, email, password_hash) VALUES (?, ?, ?)');
    if ($stmt) {
      $stmt->bind_param('sss', $username, $email, $passwordHash);
      if ($stmt->execute()) {
        $message = 'Registration successful. You can now log in.';
        $messageClass = 'notice success';
      } else {
        // Duplicate entry handling (error code 1062)
        if ($stmt->errno === 1062) {
          if (str_contains($stmt->error, 'username')) {
            $message = 'That username is already taken.';
          } elseif (str_contains($stmt->error, 'email')) {
            $message = 'An account with that email already exists.';
          } else {
            $message = 'Duplicate entry. Please use a different username/email.';
          }
        } else {
          $message = 'Error creating account: ' . htmlspecialchars($stmt->error ?: $mysqli->error);
        }
      }
      $stmt->close();
    } else {
      $message = 'Failed to prepare statement: ' . htmlspecialchars($mysqli->error);
    }
  } else {
    $message = implode(' ', $errors);
  }
}
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register — SulamProject</title>
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
  </head>
  <body>
    <main class="centered small-card">
      <h2>Register</h2>
  <?php if ($message): ?><div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div><?php endif; ?>

      <form method="post" action="register.php">
        <label>Username
          <input type="text" name="username" minlength="3" maxlength="50" required>
        </label>
        <label>Email
          <input type="email" name="email" maxlength="120" required>
        </label>
        <label>Password
          <input type="password" name="password" minlength="8" required>
        </label>
        <div class="actions">
          <button class="btn" type="submit">Create account</button>
          <a class="btn outline" href="login.php">Back to Login</a>
        </div>
      </form>
    </main>
  </body>
</html>
