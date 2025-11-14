<main class="centered small-card">
    <h2>Login</h2>
    
    <?php if (!empty($message)): ?>
        <div class="notice <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
            <?php echo e($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/sulamproject/login">
        <?php echo csrfField(); ?>
        
        <label>
            Username or Email
            <input type="text" name="username" required autofocus>
        </label>
        
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        
        <div class="actions">
            <button class="btn" type="submit">Sign in</button>
            <a class="btn outline" href="/sulamproject/register">Register</a>
        </div>
    </form>
    
    <p class="small" style="margin-top: 1.5rem; color: var(--muted);">
        Don't have an account? <a href="/sulamproject/register" style="color: var(--accent);">Create one</a>
    </p>
</main>
