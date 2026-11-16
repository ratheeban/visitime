<?php
require __DIR__ . '/config.php';
if (is_admin()) redirect('admin.php');
$pageTitle = 'Admin Login';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        redirect('admin.php');
    }
    $error = 'Invalid username or password.';
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Administrator login</h1><p>Sign in to manage Visitime destinations.</p></div></section>
<section class="section"><div class="container"><div class="form-card login-box">
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post" autocomplete="on">
  <div class="field"><label for="username">Username</label><input id="username" name="username" required autocomplete="username"></div>
  <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password"></div>
  <button class="btn" type="submit">Log in</button>
</form></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
