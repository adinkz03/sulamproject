<div class="page-header-margin">
    <!-- Hero Section with Prayer Times -->
    <div class="bento-grid" style="margin-bottom: 2rem;">
        <!-- Welcome Hero Card -->
        <div class="bento-card bento-2x2 card-hero-user">
            <div class="hero-content">
                <div class="hero-text">
                    <h2 class="hero-title">Assalamualaikum, <?php echo e($username); ?>!</h2>
                    <p class="hero-subtitle">Welcome to your community dashboard</p>
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

        <!-- Quick Action: Edit Profile -->
        <div class="bento-card bento-2x1 card-action">
            <a href="<?php echo url('profile'); ?>" class="card-action-link">
                <i class="fa-solid fa-arrow-right" style="position: absolute; top: 1rem; right: 1rem; font-size: 1.25rem; color: #94a3b8;"></i>
                <div class="bento-icon-sm icon-bg-blue">
                    <i class="fa-solid fa-user-edit text-blue"></i>
                </div>
                <h3>Edit Profile</h3>
                <p>Update your personal info</p>
            </a>
        </div>

        <!-- Quick Action: Record Death Notification -->
        <div class="bento-card bento-2x1 card-action">
            <a href="<?php echo url('features/death-funeral/user/pages/death-notification.php'); ?>" class="card-action-link">
                <i class="fa-solid fa-arrow-right" style="position: absolute; top: 1rem; right: 1rem; font-size: 1.25rem; color: #94a3b8;"></i>
                <div class="bento-icon-sm icon-bg-gray">
                    <i class="fa-solid fa-clipboard-list text-gray"></i>
                </div>
                <h3>Record Death Notification</h3>
                <p>Report a death in the community</p>
            </a>
        </div>
    </div>
        
    <!-- Featured Content Section -->
    <div style="margin-bottom: 0.5rem;">
        <h3 class="bento-title">Featured Content</h3>
    </div>
    
    <div class="bento-grid">
        <!-- Featured Donation -->
        <div class="bento-card bento-2x1 card-preview">
            <div class="preview-header">
                <h3>Donation Campaign</h3>
                <a href="<?php echo url('donations'); ?>" class="preview-link">
                    View All <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <?php if (!empty($featuredDonation)): ?>
                <div class="preview-content">
                    <div class="preview-icon">
                        <?php if (!empty($featuredDonation['image_path'])): ?>
                            <img src="<?php echo e($featuredDonation['image_path']); ?>" alt="Donation" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                        <?php else: ?>
                            <i class="fa-solid fa-hand-holding-heart"></i>
                        <?php endif; ?>
                    </div>
                    <div class="preview-text">
                        <h4><?php echo e($featuredDonation['title']); ?></h4>
                        <p><?php echo e(strlen($featuredDonation['description']) > 120 ? substr($featuredDonation['description'], 0, 120) . '...' : $featuredDonation['description']); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="preview-content preview-empty">
                    <div class="preview-icon">
                        <i class="fa-solid fa-hand-holding-heart"></i>
                    </div>
                    <div class="preview-text">
                        <h4>No Active Campaigns</h4>
                        <p>Check back soon for new donation opportunities.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Featured Event -->
        <div class="bento-card bento-2x1 card-preview">
            <div class="preview-header">
                <h3>Upcoming Event</h3>
                <a href="<?php echo url('events'); ?>" class="preview-link">
                    View All <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <?php if (!empty($featuredEvent)): ?>
                <?php 
                    $eventDate = new DateTime($featuredEvent['event_date']);
                    $dayName = $eventDate->format('l');
                    $dayNum = $eventDate->format('d');
                    $monthShort = strtoupper($eventDate->format('M'));
                    $timeStr = !empty($featuredEvent['event_time']) ? date('g:i A', strtotime($featuredEvent['event_time'])) : '';
                    $locationStr = !empty($featuredEvent['location']) ? $featuredEvent['location'] : '';
                    $details = $dayName;
                    if ($timeStr) $details .= ', ' . $timeStr;
                    if ($locationStr) $details .= ' • ' . $locationStr;
                ?>
                <div class="preview-content">
                    <div class="preview-date-box">
                        <span class="preview-date-day"><?php echo $dayNum; ?></span>
                        <span class="preview-date-month"><?php echo $monthShort; ?></span>
                    </div>
                    <div class="preview-text">
                        <h4><?php echo e($featuredEvent['title']); ?></h4>
                        <p><?php echo e($details); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="preview-content preview-empty">
                    <div class="preview-date-box" style="background: #f3f4f6;">
                        <i class="fa-solid fa-calendar-xmark" style="font-size: 1.5rem; color: #9ca3af;"></i>
                    </div>
                    <div class="preview-text">
                        <h4>No Upcoming Events</h4>
                        <p>Check back soon for scheduled activities.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
