<main class="centered small-card">
    <h2>Register</h2>
    
    <?php if (!empty($message)): ?>
        <div class="notice <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
            <?php echo e($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/sulamproject/register">
        <?php echo csrfField(); ?>
        
        <label>
            Username
            <input type="text" name="username" required autofocus pattern="[a-zA-Z0-9_]{3,20}" 
                   title="3-20 characters, letters, numbers, and underscore only">
        </label>
        
        <label>
            Email
            <input type="email" name="email" required>
        </label>
        
        <label>
            Password
            <input type="password" name="password" required minlength="8">
        </label>
        
        <label>
            Confirm Password
            <input type="password" name="confirm_password" required minlength="8">
        </label>
        
        <div class="actions">
            <button class="btn" type="submit">Create Account</button>
            <a class="btn outline" href="/sulamproject/login">Back to Login</a>
        </div>
    </form>
</main>
