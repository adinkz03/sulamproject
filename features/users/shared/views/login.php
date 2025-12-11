<div class="brand-logo">
    <i class="fa-solid fa-mosque"></i> masjidkamek
</div>

<div class="login-wrapper <?php echo ($initialView === 'register') ? 'register-mode' : ''; ?>">
    <!-- Event Carousel / Display Section -->
    <section class="event-display">
        <div class="event-card compact">
            <div class="event-image-compact">
                <div class="date-badge-small">
                    <span class="day">24</span>
                    <span class="month">NOV</span>
                </div>
                <i class="fa-solid fa-mosque fa-2x"></i>
            </div>
            <div class="event-content-compact">
                <span class="tag-small">Upcoming</span>
                <h3>Community Gathering</h3>
                <p>Friday, 8:00 PM • Main Hall</p>
                
                <div class="carousel-dots">
                    <span class="active"></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
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
            <h2>Login</h2>
            <form method="post" action="<?php echo url('login'); ?>">
                <?php echo csrfField(); ?>
                
                <label>
                    Username or Email
                    <input type="text" name="username" required <?php echo ($initialView === 'login') ? 'autofocus' : ''; ?>>
                </label>
                
                <label>
                    Password
                    <input type="password" name="password" required>
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
                        <input type="password" name="password" required minlength="8">
                    </label>
                    
                    <label>
                        Confirm Password
                        <input type="password" name="confirm_password" required minlength="8">
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
