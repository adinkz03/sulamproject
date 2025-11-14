<div class="small-card" style="max-width:980px; margin:0 auto; padding:1.2rem 1.4rem;">
    <div class="dashboard-header">
        <h2 style="margin:0">Welcome</h2>
        <div>Hi, <strong><?php echo e($username); ?></strong> <span style="color: var(--muted);">(Admin)</span></div>
    </div>

    <section class="dashboard-cards">
        <a class="dashboard-card" href="/residents">
            <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6Z"/>
                </svg>
            </span>
            <h3>Residents</h3>
            <p>Manage residents and households.</p>
        </a>
        
        <a class="dashboard-card" href="/sulamproject/assistance">
            <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11 8c0 2.21-1.79 4-4 4s-4-1.79-4-4 1.79-4 4-4 4 1.79 4 4zm0 6.72V20H0v-2c0-2.21 3.13-4 7-4 1.5 0 2.87.27 4 .72zm2.5-6.72c0-2.21 1.79-4 4-4s4 1.79 4 4-1.79 4-4 4-4-1.79-4-4zM24 20h-11v-2c0-2.21 3.13-4 7-4s7 1.79 7 4v2z"/>
                </svg>
            </span>
            <h3>Assistance</h3>
            <p>Review and approve financial aid.</p>
        </a>
        
        <a class="dashboard-card" href="/sulamproject/donations">
            <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3l2.1 4.3 4.7.7-3.4 3.3.8 4.7L12 14.8 7.8 17l.8-4.7L5.2 8l4.7-.7L12 3Zm-7 16h14v2H5v-2Z"/>
                </svg>
            </span>
            <h3>Donations</h3>
            <p>Track donations and receipts.</p>
        </a>
        
        <a class="dashboard-card" href="/sulamproject/death-funeral">
            <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                </svg>
            </span>
            <h3>Death & Funeral</h3>
            <p>Manage notifications and logistics.</p>
        </a>
        
        <a class="dashboard-card" href="/sulamproject/events">
            <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2Zm13 7H4v11h16V9Z"/>
                </svg>
            </span>
            <h3>Events</h3>
            <p>Plan and manage events.</p>
        </a>
        
        <a class="dashboard-card" href="/sulamproject/reports">
            <span class="icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                </svg>
            </span>
            <h3>Reports</h3>
            <p>Generate and export reports.</p>
        </a>
    </section>
</div>
