<div class="page-header-margin">
    <!-- Hero Section -->
    <div class="bento-grid" style="margin-bottom: 2rem;">
        <!-- Welcome Hero Card -->
        <div class="bento-card bento-2x2 card-hero">
            <div class="hero-content">
                <div class="hero-text">
                    <h2 class="hero-title">Welcome back, <?php echo e($username); ?>!</h2>
                    <p class="hero-subtitle">Manage, monitor, and report easily</p>
                </div>
                
                <!-- Date Pills -->
                <div class="hero-date-pills">
                    <div class="date-pill">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span><?php echo date('l, d M Y'); ?></span>
                    </div>
                    <?php if (!empty($hijriDate)): ?>
                        <div class="date-pill">
                            <i class="fa-solid fa-moon"></i>
                            <span><?php echo $hijriDate; ?> H</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Prayer Times in Hero -->
                <div class="hero-prayer-times">
                    <div class="hero-prayer-header">
                        <i class="fa-solid fa-mosque"></i>
                        <span>Waktu Solat · Kota Samarahan</span>
                    </div>
                    <div class="hero-prayer-grid">
                        <?php if (!empty($prayerTimes)): ?>
                            <?php foreach ($prayerTimes as $name => $time): ?>
                                <div class="hero-prayer-item">
                                    <span class="hero-prayer-name"><?php echo $name; ?></span>
                                    <span class="hero-prayer-time"><?php echo $time; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="hero-prayer-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>Unable to load prayer times</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="hero-bg-accent"></div>
        </div>

        <!-- Quick Stat: Total Residents -->
        <div class="bento-card bento-1x1 card-stat-simple">
            <div class="bento-icon-sm icon-bg-blue">
                <i class="fa-solid fa-users text-blue"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number"><?php echo $totalResidents; ?></span>
                <span class="stat-desc">Total Residents</span>
            </div>
        </div>

        <!-- Quick Stat: Active Donations -->
        <div class="bento-card bento-1x1 card-stat-simple">
             <div class="bento-icon-sm icon-bg-green">
                <i class="fa-solid fa-hand-holding-dollar text-green"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number"><?php echo $activeDonations; ?></span>
                <span class="stat-desc">Active Donations</span>
            </div>
        </div>

        <!-- Quick Stat: Active Events This Month -->
        <div class="bento-card bento-1x1 card-stat-simple">
            <div class="bento-icon-sm icon-bg-purple">
                <i class="fa-solid fa-calendar-check text-purple"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number"><?php echo $activeEvents; ?></span>
                <span class="stat-desc">Events (This Month)</span>
            </div>
        </div>

        <!-- Quick Stat: Total Balance -->
        <div class="bento-card bento-1x1 card-stat-simple">
             <div class="bento-icon-sm icon-bg-orange">
                <i class="fa-solid fa-wallet text-orange"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number">RM <?php echo number_format($totalBalance, 2); ?></span>
                <span class="stat-desc">Total Balance</span>
            </div>
        </div>
    </div>
        
    <!-- Quick Actions Section -->
    <div style="margin-bottom: 0.5rem;">
        <h3 class="bento-title">Quick Actions</h3>
    </div>
    
    <div class="bento-grid">
        <!-- Add Deposit (Financial) -->
        <a href="<?php echo url('financial/deposit-account/add'); ?>" class="bento-card bento-1x1 nav-card hover-lift">
            <div class="nav-icon-wrapper">
                <i class="fa-solid fa-plus nav-icon"></i>
            </div>
            <div class="nav-info">
                <h3>Add Deposit</h3>
                <small>Record incoming funds & zakat</small>
            </div>
        </a>

        <!-- Add Payment (Financial) -->
        <a href="<?php echo url('financial/payment-account/add'); ?>" class="bento-card bento-1x1 nav-card hover-lift">
            <div class="nav-icon-wrapper">
                <i class="fa-solid fa-file-invoice-dollar nav-icon"></i>
            </div>
            <div class="nav-info">
                <h3>Add Payment</h3>
                <small>Create voucher & expenses</small>
            </div>
        </a>

        <!-- New Event -->
        <a href="<?php echo url('events'); ?>" class="bento-card bento-1x1 nav-card hover-lift">
            <div class="nav-icon-wrapper">
                <i class="fa-solid fa-calendar-plus nav-icon"></i>
            </div>
            <div class="nav-info">
                <h3>New Event</h3>
                <small>Schedule program & activities</small>
            </div>
        </a>

        <!-- New Donation -->
        <a href="<?php echo url('donations'); ?>" class="bento-card bento-1x1 nav-card hover-lift">
            <div class="nav-icon-wrapper">
                <i class="fa-solid fa-hand-holding-heart nav-icon"></i>
            </div>
            <div class="nav-info">
                <h3>New Donation</h3>
                <small>Record donor contribution</small>
            </div>
        </a>

         <!-- Death & Funeral Management -->
         <a href="<?php echo url('death-funeral'); ?>" class="bento-card bento-2x1 nav-card hover-lift card-highlight">
            <div class="nav-flex">
                <div class="nav-icon-wrapper">
                    <i class="fa-solid fa-hands-praying nav-icon"></i>
                </div>
                <div class="nav-info">
                    <h3>Death & Funeral</h3>
                    <p>Record notifications and manage funeral assistance.</p>
                </div>
            </div>
            <div class="nav-arrow">
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="<?php echo url('users'); ?>" class="bento-card bento-2x1 nav-card hover-lift card-highlight">
            <div class="nav-flex">
                <div class="nav-icon-wrapper">
                    <i class="fa-solid fa-user-shield nav-icon"></i>
                </div>
                <div class="nav-info">
                    <h3>System Users</h3>
                    <p>Manage admin access and staff roles.</p>
                </div>
            </div>
            <div class="nav-arrow">
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

    </div>
</div>
