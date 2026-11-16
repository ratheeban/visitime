<?php
require __DIR__ . '/config.php';
require_admin();
$pageTitle = 'Manage Places';
$places = $pdo->query('SELECT * FROM places ORDER BY created_at DESC')->fetchAll();
$messagesCount = (int)$pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Admin dashboard</h1><p>Signed in as <?= e((string)($_SESSION['admin_username'] ?? 'admin')) ?>. Manage places and review site activity.</p></div></section>
<section class="section"><div class="container">
  <?php if (!empty($_GET['saved'])): ?><div class="alert success">Place saved successfully.</div><?php endif; ?>
  <?php if (!empty($_GET['deleted'])): ?><div class="alert success">Place deleted successfully.</div><?php endif; ?>
  <div class="admin-top"><div><h2 style="margin:0">Places (<?= count($places) ?>)</h2><div class="status-note"><?= $messagesCount ?> contact message<?= $messagesCount === 1 ? '' : 's' ?> received.</div></div><a class="btn" href="<?= BASE_URL ?>/place_form.php">+ Add place</a></div>
  <div class="panel table-wrap"><table class="data-table"><thead><tr><th>Place</th><th>Category</th><th>Distance</th><th>Map link</th><th>Actions</th></tr></thead><tbody>
  <?php foreach ($places as $place): ?><tr><td><strong><?= e($place['name']) ?></strong><br><span class="small-muted"><?= e($place['short_description']) ?></span></td><td><?= e($place['category']) ?></td><td><?= $place['distance_km'] !== null ? e((string)$place['distance_km']) . ' km' : '—' ?></td><td><?php if ($place['maps_url']): ?><a href="<?= e($place['maps_url']) ?>" target="_blank" rel="noopener">Open ↗</a><?php else: ?>—<?php endif; ?></td><td><a class="btn secondary" href="<?= BASE_URL ?>/place_form.php?id=<?= (int)$place['id'] ?>">Edit</a> <form class="inline-form" method="post" action="<?= BASE_URL ?>/delete_place.php" onsubmit="return confirm('Delete this place?')"><input type="hidden" name="id" value="<?= (int)$place['id'] ?>"><button class="btn danger" type="submit">Delete</button></form></td></tr><?php endforeach; ?>
  <?php if (!$places): ?><tr><td colspan="5">No places yet.</td></tr><?php endif; ?>
  </tbody></table></div>
  <p style="margin-top:20px"><a class="text-link" href="<?= BASE_URL ?>/messages.php">View contact messages →</a></p>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
