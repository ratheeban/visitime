<?php
require __DIR__ . '/config.php';
require_admin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$place = ['name'=>'','image'=>'','category'=>'','short_description'=>'','distance_km'=>'','maps_url'=>''];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM places WHERE id = ?');
    $stmt->execute([$id]); $found = $stmt->fetch();
    if (!$found) { http_response_code(404); exit('Place not found.'); }
    $place = $found;
}
$pageTitle = $id ? 'Edit Place' : 'Add Place';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $place['name'] = trim((string)($_POST['name'] ?? ''));
    $place['category'] = trim((string)($_POST['category'] ?? ''));
    $place['short_description'] = trim((string)($_POST['short_description'] ?? ''));
    $place['distance_km'] = trim((string)($_POST['distance_km'] ?? ''));
    $place['maps_url'] = trim((string)($_POST['maps_url'] ?? ''));
    $imagePath = (string)($place['image'] ?? '');
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Image upload failed. Please try again.';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $error = 'Image must be 5 MB or smaller.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
            if (!isset($allowed[$mime])) {
                $error = 'Upload a JPG, PNG, or WEBP image.';
            } else {
                $uploadDir = __DIR__ . '/uploads';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
                if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
                    $error = 'Could not save the uploaded image. Check folder permissions.';
                } else {
                    $imagePath = 'uploads/' . $filename;
                }
            }
        }
    }
    if ($place['name'] === '' || $place['category'] === '' || $place['short_description'] === '') {
        $error = $error ?: 'Name, category, and short description are required.';
    } elseif (mb_strlen($place['name']) > 180 || mb_strlen($place['category']) > 100 || mb_strlen($place['short_description']) > 500) {
        $error = 'One or more fields exceed the allowed length.';
    } elseif ($place['distance_km'] !== '' && (!is_numeric($place['distance_km']) || (float)$place['distance_km'] < 0)) {
        $error = 'Distance must be a non-negative number.';
    } elseif ($place['maps_url'] !== '' && !filter_var($place['maps_url'], FILTER_VALIDATE_URL)) {
        $error = 'Google Maps location must be a valid URL.';
    }
    if ($error === '') {
        $distance = $place['distance_km'] === '' ? null : (float)$place['distance_km'];
        $maps = $place['maps_url'] === '' ? null : $place['maps_url'];
        if ($id) {
            $stmt = $pdo->prepare('UPDATE places SET name=?, image=?, category=?, short_description=?, distance_km=?, maps_url=? WHERE id=?');
            $stmt->execute([$place['name'], $imagePath ?: null, $place['category'], $place['short_description'], $distance, $maps, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO places (name,image,category,short_description,distance_km,maps_url) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$place['name'], $imagePath ?: null, $place['category'], $place['short_description'], $distance, $maps]);
        }
        redirect('admin.php?saved=1');
    }
    $place['image'] = $imagePath;
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1><?= $id ? 'Edit place' : 'Add a place' ?></h1><p>Provide destination details for the public directory.</p></div></section>
<section class="section"><div class="container"><div class="form-card" style="max-width:850px;margin:auto">
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <div class="form-grid">
    <div class="field"><label for="name">Place name *</label><input id="name" name="name" maxlength="180" required value="<?= e((string)$place['name']) ?>"></div>
    <div class="field"><label for="category">Category *</label><input id="category" name="category" maxlength="100" required placeholder="Beach, Historical, Nature..." value="<?= e((string)$place['category']) ?>"></div>
    <div class="field"><label for="distance_km">Distance (km)</label><input id="distance_km" name="distance_km" type="number" min="0" step="0.01" value="<?= e((string)($place['distance_km'] ?? '')) ?>"></div>
    <div class="field"><label for="maps_url">Google Maps location URL</label><input id="maps_url" name="maps_url" type="url" maxlength="700" placeholder="https://www.google.com/maps/..." value="<?= e((string)($place['maps_url'] ?? '')) ?>"></div>
    <div class="field full"><label for="short_description">Short description *</label><textarea id="short_description" name="short_description" maxlength="500" required><?= e((string)$place['short_description']) ?></textarea></div>
    <div class="field full"><label for="image">Place image (JPG, PNG, WEBP; max 5 MB)</label><input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><?php if (!empty($place['image'])): ?><span class="small-muted">Current image: <?= e((string)$place['image']) ?></span><img src="<?= BASE_URL ?>/<?= e((string)$place['image']) ?>" alt="Current place image" style="max-width:220px;border-radius:10px"><?php endif; ?></div>
  </div>
  <button class="btn" type="submit">Save place</button> <a class="btn secondary" href="<?= BASE_URL ?>/admin.php">Cancel</a>
</form></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
