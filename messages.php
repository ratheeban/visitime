<?php
require __DIR__ . '/config.php';
require_admin();
$pageTitle = 'Contact Messages';
$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Contact messages</h1><p>Messages submitted through the public contact form.</p></div></section>
<section class="section"><div class="container"><div class="panel table-wrap"><table class="data-table"><thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Message</th></tr></thead><tbody>
<?php foreach ($messages as $m): ?><tr><td><?= e($m['created_at']) ?></td><td><?= e($m['name']) ?></td><td><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></td><td><?= nl2br(e($m['message'])) ?></td></tr><?php endforeach; ?>
<?php if (!$messages): ?><tr><td colspan="4">No messages received.</td></tr><?php endif; ?>
</tbody></table></div><p style="margin-top:18px"><a class="btn secondary" href="<?= BASE_URL ?>/admin.php">Back to admin</a></p></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
