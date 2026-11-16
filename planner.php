<?php
require __DIR__ . '/config.php';
$pageTitle = 'Day Planner';
$places = $pdo->query('SELECT id, name, category, short_description, distance_km, image FROM places ORDER BY name')->fetchAll();

$selectedPlaces = [];
$errorMessage = '';
$tripDate = trim((string)($_POST['trip_date'] ?? date('Y-m-d')));
$startName = trim((string)($_POST['start_name'] ?? 'Starting point'));
if ($startName === '') $startName = 'Starting point';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_route'])) {
    $ids = $_POST['stops'] ?? [];
    if (!is_array($ids)) $ids = [];
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn($id) => $id > 0)));

    if (!$ids) {
        $errorMessage = 'Select at least one destination to create your route.';
    } else {
        $placeMap = [];
        foreach ($places as $place) $placeMap[(int)$place['id']] = $place;
        foreach ($ids as $id) {
            if (isset($placeMap[$id])) $selectedPlaces[] = $placeMap[$id];
        }
        if (!$selectedPlaces) {
            $errorMessage = 'The selected destinations could not be found. Please select them again.';
        } else {
            // The database distance is treated as each destination's distance from the same starting point.
            usort($selectedPlaces, static function ($a, $b) {
                $da = is_numeric($a['distance_km']) ? (float)$a['distance_km'] : null;
                $db = is_numeric($b['distance_km']) ? (float)$b['distance_km'] : null;
                if ($da === null && $db === null) return strcasecmp($a['name'], $b['name']);
                if ($da === null) return 1;
                if ($db === null) return -1;
                return ($da <=> $db) ?: strcasecmp($a['name'], $b['name']);
            });
            foreach ($selectedPlaces as $place) {
                if (!is_numeric($place['distance_km'])) {
                    $errorMessage = 'Some selected destinations do not have a numeric distance in the database. Add their distance in the admin page to build the full route.';
                    break;
                }
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<style>
.planner-layout{display:grid;grid-template-columns:minmax(0,1fr);gap:24px;align-items:start}
.route-board{background:#f6faf7;border:1px solid #dce9df;border-radius:18px;padding:22px;margin-top:22px}
.route-board h2{margin:0 0 6px}.route-note{color:#53665a;font-size:.94rem;margin:0 0 20px}
.route-line{position:relative;max-width:760px;margin:0 auto;padding:4px 0}
.route-stop{display:grid;grid-template-columns:54px minmax(0,1fr) auto;gap:14px;align-items:center;position:relative;min-height:84px}
.route-marker{z-index:1;width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#176b45;color:#fff;font-weight:800;border:4px solid #dcefe2;box-shadow:0 0 0 1px #176b45}
.route-stop.start .route-marker{background:#203b2d;box-shadow:0 0 0 1px #203b2d}
.route-stop:not(:last-child):before{content:"";position:absolute;left:20px;top:50px;bottom:-35px;width:3px;background:#8ebba0}
.route-stop-info{background:#fff;border:1px solid #e1eae3;border-radius:12px;padding:13px 15px;min-width:0}
.route-stop-info strong{display:block;overflow-wrap:anywhere}.route-stop-info small{color:#66756b}
.route-distance{font-size:.9rem;font-weight:700;color:#176b45;text-align:right;white-space:nowrap}
.route-leg{grid-column:2/4;color:#52675a;font-size:.86rem;padding:0 0 12px 14px;margin-top:-4px}
.route-summary{display:flex;flex-wrap:wrap;gap:12px;margin-top:20px}
.route-stat{flex:1 1 150px;background:#fff;border:1px solid #dce9df;border-radius:12px;padding:14px 16px}.route-stat span{display:block;color:#607166;font-size:.85rem}.route-stat strong{font-size:1.25rem}
.planner-place{display:flex;gap:12px;align-items:flex-start}.planner-place input{margin-top:5px}.planner-place-main{flex:1;min-width:0}.planner-place-meta{display:flex;gap:8px;flex-wrap:wrap;margin-top:4px}.distance-tag{font-size:.82rem;color:#176b45;font-weight:700}.place-thumb{width:58px;height:58px;object-fit:cover;border-radius:9px;background:#e7eee8;flex-shrink:0}
@media(max-width:600px){.route-board{padding:14px}.route-stop{grid-template-columns:42px minmax(0,1fr);gap:10px}.route-marker{width:34px;height:34px;border-width:3px;font-size:.85rem}.route-stop:not(:last-child):before{left:16px;top:43px}.route-distance{grid-column:2;text-align:left;margin-top:-6px}.route-leg{grid-column:2;padding-left:8px}.route-stop-info{padding:11px}}
</style>
<section class="page-hero"><div class="container"><h1>One-day trip planner</h1><p>Choose destinations and view a distance-based route right here on Visitime.</p></div></section>
<section class="section"><div class="container planner-layout">
<?php if (!$places): ?>
  <div class="empty-state">No destinations are available yet. Add places in the admin page first.</div>
  
  <div id="plannerError"></div>
  
<?php else: ?>
<form class="form-card" id="plannerForm" method="post" action="">
  <div class="field"><label for="start_name">Starting point name</label><input id="start_name" name="start_name" value="<?= e($startName === 'Starting point' ? '' : $startName) ?>" placeholder="e.g. Hotel or town centre"><small class="small-muted">This is a label for the route diagram.</small></div>
  <div class="field"><label for="trip_date">Travel date</label><input id="trip_date" name="trip_date" type="date" value="<?= e($tripDate) ?>"></div>
  <div class="section-heading"><div><h2>Choose your destinations</h2><p>Distances are read from the database and destinations are arranged from nearest to farthest.</p></div></div>
  <div class="planner-list">
  <?php foreach ($places as $place): $checked = in_array((int)$place['id'], array_map(static fn($p) => (int)$p['id'], $selectedPlaces), true); ?>
    <label class="planner-item planner-place">
      <input type="checkbox" name="stops[]" value="<?= (int)$place['id'] ?>" <?= $checked ? 'checked' : '' ?>>
      <?php if (!empty($place['image'])): ?><img class="place-thumb" src="<?= e($place['image']) ?>" alt=""><?php endif; ?>
      <span class="planner-place-main"><strong><?= e($place['name']) ?></strong><span class="planner-place-meta"><span class="pill"><?= e($place['category']) ?></span><span class="distance-tag"><?= is_numeric($place['distance_km']) ? e(number_format((float)$place['distance_km'], 2)) . ' km' : 'Distance not entered' ?></span></span><span class="small-muted"><?= e($place['short_description']) ?></span></span>
    </label>
  <?php endforeach; ?>
  </div>
  <div style="margin-top:22px"><button class="btn" type="submit" name="create_route" value="1">Create route</button></div>
  <?php if ($errorMessage !== ''): ?><p class="alert error" style="margin-top:16px"><?= e($errorMessage) ?></p><?php endif; ?>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_route']) && $selectedPlaces && $errorMessage === ''): ?>
<section class="route-board" aria-labelledby="routeTitle">
  <h2 id="routeTitle">Your distance-based route</h2>
  <p class="route-note">Travel date: <?= e($tripDate) ?> · Ordered by the distance.</p>
  <div class="route-line">
    <div class="route-stop start"><div class="route-marker">S</div><div class="route-stop-info"><strong><?= e($startName) ?></strong><small>Route start</small></div><div class="route-distance">Start</div></div>
    <?php $previousDistance = 0.0; $lastDistance = 0.0; $stopNo = 1; foreach ($selectedPlaces as $place): $distance = (float)$place['distance_km']; $leg = max(0, $distance - $previousDistance); $lastDistance = max($lastDistance, $distance); ?>
      <div class="route-leg">↓ <?= number_format($leg, 2) ?> km from previous distance mark</div>
      <div class="route-stop"><div class="route-marker"><?= $stopNo ?></div><div class="route-stop-info"><strong><?= e($place['name']) ?></strong><small><?= e($place['category']) ?></small></div><div class="route-distance"><?= number_format($distance, 2) ?> km from start</div></div>
    <?php $previousDistance = $distance; $stopNo++; endforeach; ?>
  </div>
  <div class="route-summary"><div class="route-stat"><span>Selected destinations</span><strong><?= count($selectedPlaces) ?></strong></div><div class="route-stat"><span>Farthest entered distance</span><strong><?= number_format($lastDistance, 2) ?> km</strong></div><div class="route-stat"><span>Route calculation</span><strong>Distances</strong></div></div>
 
  <div style="margin-top:18px"><button class="btn btn-secondary" type="button" onclick="window.print()">Print itinerary</button></div>
</section>
<?php endif; ?>
<?php endif; ?>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
