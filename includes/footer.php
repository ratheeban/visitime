</main>
<footer class="site-footer">
    <div class="container footer-inner">
        <div><strong>Visitime</strong><p>Discover places, plan a day, make memories.</p></div>
        <div class="footer-links"><a href="<?= BASE_URL ?>/explore.php">Explore</a><a href="<?= BASE_URL ?>/planner.php">Day Planner</a><a href="<?= BASE_URL ?>/contact.php">Contact</a></div>
    </div>
    <div class="copyright">© <?= date('Y') ?> Visitime. All rights reserved.</div>
</footer>
<script>
(function () {
    const el = document.getElementById('headerDateTime');
    if (!el) return;
    function updateDateTime() {
        const now = new Date();
        el.innerHTML = '<span>' + now.toLocaleDateString(undefined, {weekday:'short', day:'2-digit', month:'short', year:'numeric'}) + '</span><span>' +
            now.toLocaleTimeString(undefined, {hour:'2-digit', minute:'2-digit', second:'2-digit'}) + '</span>';
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
})();
</script>
</body>
</html>
