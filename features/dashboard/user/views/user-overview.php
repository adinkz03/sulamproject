<div class="small-card" style="max-width:980px; margin:0 auto; padding:1.2rem 1.4rem;">
    <div class="dashboard-header">
        <h2 style="margin:0">Welcome</h2>
        <div>Hi, <strong><?php echo e($username); ?></strong></div>
    </div>

    <section class="dashboard-cards">
        <a class="dashboard-card" href="/residents">
            <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6Z"/>
                </svg>
            </span>
            <h3>Residents</h3>
            <p>View and update resident information.</p>
        </a>
        
        <a class="dashboard-card" href="/sulamproject/donations">
            <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3l2.1 4.3 4.7.7-3.4 3.3.8 4.7L12 14.8 7.8 17l.8-4.7L5.2 8l4.7-.7L12 3Zm-7 16h14v2H5v-2Z"/>
                </svg>
            </span>
            <h3>Donations</h3>
            <p>Record your donations.</p>
        </a>
        
        <a class="dashboard-card" href="/sulamproject/events">
            <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2Zm13 7H4v11h16V9Z"/>
                </svg>
            </span>
            <h3>Events</h3>
            <p>View upcoming events.</p>
        </a>
    </section>
</div>
