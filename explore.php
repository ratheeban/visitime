<?php
require __DIR__ . '/config.php';
$pageTitle = 'Explore Places';
$q = trim((string)($_GET['q'] ?? ''));
$category = trim((string)($_GET['category'] ?? ''));
$categories = $pdo->query('SELECT DISTINCT category FROM places ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
$sql = 'SELECT * FROM places WHERE 1=1';
$params = [];
if ($q !== '') { $sql .= ' AND (name LIKE ? OR short_description LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($category !== '') { $sql .= ' AND category = ?'; $params[] = $category; }
$sql .= ' ORDER BY name';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $places = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Explore places</h1><p>Search destinations and find the details you need before you go.</p></div></section>
<section class="section"><div class="container">
  <form class="filters" method="get">
    <div class="form-grid">
      <div class="field"><label for="q">Search</label><input id="q" name="q" value="<?= e($q) ?>" placeholder="Place name or description"></div>
      <div class="field"><label for="category">Category</label><select id="category" name="category"><option value="">All categories</option><?php foreach ($categories as $c): ?><option value="<?= e($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></div>
    </div><button class="btn" type="submit">Search places</button> <a class="btn secondary" href="<?= BASE_URL ?>/explore.php">Reset</a>
  </form>
  <div class="section-heading"><div><h2><?= count($places) ?> result<?= count($places) === 1 ? '' : 's' ?></h2><p>Choose a destination to view it on the map.</p></div></div>
  <?php if ($places): ?><div class="cards"><?php foreach ($places as $place): ?>
    <article class="place-card"><div class="place-image"><?php if (!empty($place['image'])): ?><img src="<?= BASE_URL ?>/<?= e($place['image']) ?>" alt="<?= e($place['name']) ?>"><?php else: ?>⌖<?php endif; ?></div><div class="place-content"><span class="pill"><?= e($place['category']) ?></span><h3><?= e($place['name']) ?></h3><p><?= e($place['short_description']) ?></p><div class="place-meta"><span><?= $place['distance_km'] !== null ? e((string)$place['distance_km']) . ' km' : 'Distance not set' ?></span><a href="<?= e($place['maps_url'] ?: 'https://www.google.com/maps/search/?api=1&query=' . urlencode($place['name'])) ?>" target="_blank" rel="noopener">Open map ↗</a></div></div></article>
  <?php endforeach; ?></div><?php else: ?><div class="empty-state">No matching places found. Try another search or category.</div><?php endif; ?>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
