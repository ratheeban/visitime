<?php
require __DIR__ . '/config.php';
$pageTitle = 'Discover Places';
$stmt = $pdo->query('SELECT * FROM places ORDER BY created_at DESC LIMIT 6');
$places = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="container hero-content">
    <div class="eyebrow">Your next journey starts here</div>
    <h1>Discover places.<br>Plan better days.</h1>
    <p>Find inspiring destinations, explore local attractions, and build a simple one-day travel plan with Visitime.</p>
    <div class="card-actions"><a class="btn light" href="<?= BASE_URL ?>/explore.php">Explore places →</a><a class="btn secondary" href="<?= BASE_URL ?>/planner.php">Plan a day</a></div>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="section-heading"><div><h2>Places to explore</h2><p>Discover destinations worth adding to your itinerary.</p></div><a class="text-link" href="<?= BASE_URL ?>/explore.php">View all places →</a></div>
    <?php if ($places): ?><div class="cards">
      <?php foreach ($places as $place): ?>
      <article class="place-card">
        <div class="place-image"><?php if (!empty($place['image'])): ?><img src="<?= BASE_URL ?>/<?= e($place['image']) ?>" alt="<?= e($place['name']) ?>"><?php else: ?>⌖<?php endif; ?></div>
        <div class="place-content"><span class="pill"><?= e($place['category']) ?></span><h3><?= e($place['name']) ?></h3><p><?= e($place['short_description']) ?></p><div class="place-meta"><span><?= $place['distance_km'] !== null ? e((string)$place['distance_km']) . ' km away' : 'Distance not set' ?></span><a href="<?= e($place['maps_url'] ?: 'https://www.google.com/maps/search/?api=1&query=' . urlencode($place['name'])) ?>" target="_blank" rel="noopener">Open map ↗</a></div></div>
      </article>
      <?php endforeach; ?>
    </div><?php else: ?><div class="empty-state">No places have been added yet.</div><?php endif; ?>
  </div>
</section>
<section class="section soft">
  <div class="container">
    <div class="section-heading"><div><h2>Make the most of your day</h2><p>Useful tools to make travel planning straightforward.</p></div></div>
    <div class="feature-grid">
      <div class="feature"><div class="feature-icon">🧭</div><h3>Explore destinations</h3><p>Search by place name or category and open locations in Google Maps.</p></div>
      <div class="feature"><div class="feature-icon">🗓️</div><h3>Build a day plan</h3><p>Select places and create a Google Maps route for your chosen stops.</p></div>
      <div class="feature"><div class="feature-icon">📍</div><h3>Useful place details</h3><p>View photos, descriptions, categories, distance information, and map links.</p></div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
