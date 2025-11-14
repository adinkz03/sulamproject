<?php
require_once __DIR__ . '/../../lib/utilities/functions.php';
?>
<div class="dashboard">
    <aside class="sidebar">
        <div class="brand">OurMasjid</div>
        <nav class="nav">
            <a href="/sulamproject/dashboard">Dashboard</a>
            <a href="/sulamproject/residents">Residents</a>
            <a href="/sulamproject/donations">Donations</a>
            <a href="/sulamproject/events">Events</a>
            <?php if (isAdmin()): ?>
                <a href="/sulamproject/reports">Reports</a>
            <?php endif; ?>
            <a href="/sulamproject/logout">Logout</a>
        </nav>
    </aside>

    <main class="content">
        <?php echo $content ?? ''; ?>
    </main>
</div>
