<?php
require __DIR__ . '/config.php';
$pageTitle = 'Contact';
$success = false; $error = '';
$name = ''; $email = ''; $message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        $error = 'Please provide your name, a valid email address, and a message.';
    } elseif (mb_strlen($name) > 120 || mb_strlen($message) > 5000) {
        $error = 'Your name or message is too long.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $message]);
        $success = true; $name = ''; $email = ''; $message = '';
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Contact Visitime</h1><p>Send a question or suggestion about destinations listed on the site.</p></div></section>
<section class="section"><div class="container"><div class="form-card" style="max-width:760px;margin:auto">
<?php if ($success): ?><div class="alert success">Your message has been submitted successfully.</div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post">
  <div class="field"><label for="name">Name</label><input id="name" name="name" maxlength="120" required value="<?= e($name) ?>"></div>
  <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" maxlength="190" required value="<?= e($email) ?>"></div>
  <div class="field"><label for="message">Message</label><textarea id="message" name="message" maxlength="5000" required><?= e($message) ?></textarea></div>
  <button class="btn" type="submit">Send message</button>
</form></div> <br>
<div class="contact-social" aria-label="Other ways to contact Visitime">

    <!-- Facebook -->
    <a class="contact-social-link"
       href="https://www.facebook.com/"
       target="_blank"
       rel="noopener noreferrer">

        <img src="<?= BASE_URL ?>/assets/facebook.png"
             alt=""
             width="28"
             height="28">

        <span>
            <strong>Follow us on Facebook</strong>
            <small>See updates and travel inspiration</small>
        </span>
    </a>

    <!-- Email -->
    <a class="contact-social-link"
       href="mailto:info@visitime.com">

        <img src="<?= BASE_URL ?>/assets/email.png"
             alt=""
             width="28"
             height="28">

        <span>
            <strong>Email us</strong>
            <small>Send us your questions and suggestions</small>
        </span>
    </a>

</div>

</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
