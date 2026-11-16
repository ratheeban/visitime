<?php
require __DIR__ . '/config.php';
$count = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
if ($count > 0) {
    exit('An administrator already exists. For security, remove setup_admin.php from the server.');
}
$username = 'admin';
$password = 'ChangeMe123!';
$stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
$stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
?>
<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Visitime setup</title>
<body style="font-family:system-ui;max-width:680px;margin:50px auto;padding:20px;line-height:1.6">
<h1>Administrator created</h1>
<p><strong>Username:</strong> admin<br><strong>Temporary password:</strong> ChangeMe123!</p>
<p>Log in, change the password as soon as possible, then delete <code>setup_admin.php</code> from your server.</p>
<p><a href="<?= BASE_URL ?>/login.php">Go to admin login</a></p>
</body></html>
