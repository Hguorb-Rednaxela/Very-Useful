<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="container">
    <h1 id="company_name">RIGET ZOO ADVENTURES</h1>
    <h1 id="opening_times">OPEN MONDAY–SUNDAY 7AM–6PM</h1>

    <div id="login-status">
        <?php if (isset($_SESSION['user_email'])): ?>
            <h1>Logged in as: <?= htmlspecialchars($_SESSION['user_email'])?></h1> 
        <?php else: ?>
            <h1>Not logged in</h1>
        <?php endif; ?>
    </div>
</div>
