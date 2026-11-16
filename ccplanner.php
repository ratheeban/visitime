<?php
require __DIR__ . '/config.php';
$pageTitle = 'Day Planner';
$places = $pdo->query('SELECT id, name, category, short_description, maps_url FROM places ORDER BY name')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>One-day trip planner</h1><p>Select the places you want to visit and create a route in Google Maps.</p></div></section>
<section class="section"><div class="container">
<?php if (!$places): ?><div class="empty-state">No places are available yet. Add destinations to the directory first.</div><?php else: ?>
<form class="form-card" id="plannerForm">
  <div class="field"><label for="start">Starting point (optional)</label><input id="start" placeholder="e.g. Jaffna town or your hotel"></div>
  <div class="field"><label for="day">Travel date</label><input id="day" type="date"></div>
  <div class="section-heading"><div><h2>Choose your stops</h2><p>Select destinations in the order you would like to visit them.</p></div></div>
  <div class="planner-list">
    <?php foreach ($places as $place): ?><label class="planner-item"><input type="checkbox" name="stops[]" value="<?= (int)$place['id'] ?>" data-name="<?= e($place['name']) ?>"><span><strong><?= e($place['name']) ?></strong><span class="pill"><?= e($place['category']) ?></span><span class="small-muted"><?= e($place['short_description']) ?></span></span></label><?php endforeach; ?>
  </div>
  <div class="field" style="margin-top:22px"><label for="order">Route order (optional)</label><input id="order" placeholder="Enter selected place names separated by commas"></div>
  <p class="small-muted">If route order is blank, selected destinations are added alphabetically. Google Maps opens in a new tab.</p>
  <button class="btn" type="submit">Create Google Maps route ↗</button>
  <p id="plannerError" class="alert error" hidden></p>
</form>
<?php endif; ?>
</div></section>
<script>
const plannerForm = document.getElementById('plannerForm');
if (plannerForm) {
  const day = document.getElementById('day');
  day.value = new Date().toISOString().slice(0,10);
  plannerForm.addEventListener('submit', function(event) {
    event.preventDefault();
    const selected = [...plannerForm.querySelectorAll('input[name="stops[]"]:checked')].map(el => el.dataset.name);
    const error = document.getElementById('plannerError');
    if (!selected.length) { error.hidden = false; error.textContent = 'Select at least one destination.'; return; }
    const typedOrder = document.getElementById('order').value.trim();
    let stops = selected;
    if (typedOrder) {
      const requested = typedOrder.split(',').map(s => s.trim()).filter(Boolean);
      const selectedLower = new Set(selected.map(s => s.toLowerCase()));
      const valid = requested.filter(s => selectedLower.has(s.toLowerCase()));
      const validLower = new Set(valid.map(s => s.toLowerCase()));
      stops = [...valid, ...selected.filter(s => !validLower.has(s.toLowerCase()))];
    } else { stops.sort((a,b) => a.localeCompare(b)); }
    const start = document.getElementById('start').value.trim();
    const origin = start || stops[0];
    const destinations = start ? stops : stops.slice(1);
    const destination = start ? stops[stops.length - 1] : (stops[stops.length - 1] || stops[0]);
    const waypoints = start ? stops.slice(0, -1) : stops.slice(1, -1);
    const params = new URLSearchParams({api:'1', origin, destination, travelmode:'driving'});
    if (waypoints.length) params.set('waypoints', waypoints.join('|'));
    window.open('https://www.google.com/maps/dir/?' + params.toString(), '_blank', 'noopener');
    error.hidden = true;
  });
}
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
