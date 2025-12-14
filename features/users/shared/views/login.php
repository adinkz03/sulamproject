<?php
$carouselEvents = isset($carouselEvents) && is_array($carouselEvents) ? $carouselEvents : [];

$carouselPayload = [];
foreach ($carouselEvents as $evt) {
    $eventDate = !empty($evt['event_date']) ? (string)$evt['event_date'] : '';
    $eventTime = !empty($evt['event_time']) ? (string)$evt['event_time'] : '';
    $location = !empty($evt['location']) ? (string)$evt['location'] : '';

    $day = '';
    $month = '';
    $dayName = '';
    if ($eventDate !== '') {
        $ts = strtotime($eventDate);
        if ($ts !== false) {
            $day = date('d', $ts);
            $month = strtoupper(date('M', $ts));
            $dayName = date('l', $ts);
        }
    }

    $timeLabel = '';
    if ($eventTime !== '') {
        $tst = strtotime($eventTime);
        if ($tst !== false) {
            $timeLabel = date('g:i A', $tst);
        }
    }

    $imageUrl = '';
    if (!empty($evt['image_path'])) {
        $imgSrc = (string)$evt['image_path'];
        if (!str_starts_with($imgSrc, 'http')) {
            $imgSrc = url($imgSrc);
        }
        $imageUrl = $imgSrc;
    }

    $meta = '';
    if ($dayName !== '' && $timeLabel !== '') {
        $meta = $dayName . ', ' . $timeLabel;
    } elseif ($dayName !== '') {
        $meta = $dayName;
    } elseif ($timeLabel !== '') {
        $meta = $timeLabel;
    }
    if ($location !== '') {
        $meta = $meta !== '' ? ($meta . ' • ' . $location) : $location;
    }

    $carouselPayload[] = [
        'id' => isset($evt['id']) ? (int)$evt['id'] : 0,
        'title' => (string)($evt['title'] ?? ''),
        'day' => $day,
        'month' => $month,
        'meta' => $meta,
        'image' => $imageUrl,
    ];
}

$carouselFirst = $carouselPayload[0] ?? ['title' => 'No upcoming events', 'day' => '', 'month' => '', 'meta' => '', 'image' => ''];
$carouselJson = json_encode(
    $carouselPayload,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
?>

<div class="brand-logo">
    <i class="fa-solid fa-mosque"></i> masjidkamek
</div>

<div class="login-wrapper <?php echo ($initialView === 'register') ? 'register-mode' : ''; ?>">
    <!-- Event Carousel / Display Section -->
    <section class="event-display">
        <div class="event-card compact" data-login-events-carousel>
            <div class="event-image-compact">
                <img class="event-carousel-image" data-event-image src="<?php echo !empty($carouselFirst['image']) ? e($carouselFirst['image']) : ''; ?>" alt="" <?php echo !empty($carouselFirst['image']) ? '' : 'style="display:none;"'; ?>>
                <div class="date-badge-small">
                    <span class="day" data-event-day><?php echo e($carouselFirst['day']); ?></span>
                    <span class="month" data-event-month><?php echo e($carouselFirst['month']); ?></span>
                </div>
                <i class="fa-solid fa-mosque fa-2x" data-event-fallback-icon <?php echo !empty($carouselFirst['image']) ? 'style="display:none;"' : ''; ?>></i>
            </div>
            <div class="event-content-compact">
                <span class="tag-small">Upcoming</span>
                <h3 data-event-title><?php echo e($carouselFirst['title']); ?></h3>
                <p data-event-meta><?php echo e($carouselFirst['meta']); ?></p>
                
                <?php if (count($carouselPayload) > 1): ?>
                    <div class="carousel-dots" data-carousel-dots>
                        <?php foreach ($carouselPayload as $idx => $item): ?>
                            <button type="button" class="<?php echo $idx === 0 ? 'active' : ''; ?>" data-carousel-index="<?php echo (int)$idx; ?>" aria-label="Show event <?php echo (int)($idx + 1); ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="carousel-dots" data-carousel-dots style="display:none;"></div>
                <?php endif; ?>
            </div>
        </div>

        <script type="application/json" id="loginEventsCarouselData"><?php echo $carouselJson ?: '[]'; ?></script>
    </section>

    <!-- Login Card -->
    <main class="login-card" data-login-url="<?php echo url('login'); ?>" data-register-url="<?php echo url('register'); ?>">
        <?php if (!empty($message)): ?>
            <div class="notice <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
                <?php echo e($message); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <div id="login-form" style="<?php echo ($initialView === 'login') ? '' : 'display: none;'; ?>">
            <div class="login-logo">
                <img src="<?php echo url('assets/uploads/masjid_logo.jpg'); ?>" alt="Masjid Logo">
            </div>
            <h2>Login</h2>
            <form method="post" action="<?php echo url('login'); ?>">
                <?php echo csrfField(); ?>
                
                <label>
                    Username or Email
                    <input type="text" name="username" required <?php echo ($initialView === 'login') ? 'autofocus' : ''; ?>>
                </label>
                
                <label>
                    Password
                    <div class="password-field">
                        <input type="password" name="password" id="login_password" required autocomplete="current-password">
                        <button
                            type="button"
                            class="password-toggle"
                            aria-label="Show password"
                            aria-pressed="false"
                            data-toggle-password="login_password"
                        >
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </label>
                
                <div class="actions">
                    <button class="btn" type="submit">Sign in</button>
                    <div class="register-row">
                        <span>Don't have an account?</span>
                        <a class="link" href="#" onclick="toggleAuth('register'); return false;">Register</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Register Form -->
        <div id="register-form" style="<?php echo ($initialView === 'register') ? '' : 'display: none;'; ?>">
            <div class="login-logo">
                <img src="<?php echo url('assets/uploads/masjid_logo.jpg'); ?>" alt="Masjid Logo">
            </div>
            <h2>Register</h2>
            <form method="post" action="<?php echo url('register'); ?>">
                <?php echo csrfField(); ?>
                
                <label>
                    Name
                    <input type="text" name="name" required maxlength="120" <?php echo ($initialView === 'register') ? 'autofocus' : ''; ?>>
                </label>

                <div class="form-row">
                    <label>
                        Username
                        <input type="text" name="username" required pattern="[a-zA-Z0-9_]{3,20}" 
                               title="3-20 characters, letters, numbers, and underscore only">
                    </label>

                    <label>
                        Email
                        <input type="email" name="email" required>
                    </label>
                </div>
                
                <div class="form-row">
                    <label>
                        Phone Number
                        <input type="tel" name="phone_number" maxlength="20">
                    </label>

                    <label>
                        Marital Status
                        <select name="marital_status">
                            <option value="">Select Status</option>
                            <option value="single">Single</option>
                            <option value="married">Married</option>
                            <option value="divorced">Divorced</option>
                            <option value="widowed">Widowed</option>
                            <option value="others">Others</option>
                        </select>
                    </label>
                </div>

                <label>
                    Address
                    <textarea name="address" rows="2" placeholder="House No, Street, Area..."></textarea>
                </label>

                <label>
                    Monthly Income Range
                    <select name="income_range">
                        <option value="">Select Income Range</option>
                        <option value="below_5250">Below RM5,250</option>
                        <option value="between_5250_11820">RM5,250 - RM11,820</option>
                        <option value="above_11820">Above RM11,820</option>
                    </select>
                </label>
                
                <div class="form-row">
                    <label>
                        Password
                        <div class="password-field">
                            <input type="password" name="password" id="register_password" required minlength="8" autocomplete="new-password">
                            <button
                                type="button"
                                class="password-toggle"
                                aria-label="Show password"
                                aria-pressed="false"
                                data-toggle-password="register_password"
                            >
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </label>
                    
                    <label>
                        Confirm Password
                        <div class="password-field">
                            <input type="password" name="confirm_password" id="register_confirm_password" required minlength="8" autocomplete="new-password">
                            <button
                                type="button"
                                class="password-toggle"
                                aria-label="Show password"
                                aria-pressed="false"
                                data-toggle-password="register_confirm_password"
                            >
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </label>
                </div>
                
                <div class="actions">
                    <button class="btn" type="submit">Create Account</button>
                    <div class="register-row">
                        <span>Already have an account?</span>
                        <a class="link" href="#" onclick="toggleAuth('login'); return false;">Login</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
