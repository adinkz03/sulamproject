<?php
// Reusable sidebar template under features. Set $currentPage if needed.
$ROOT = dirname(__DIR__, 3);
require_once $ROOT . '/features/shared/lib/auth/session.php';
initSecureSession();
$isAdmin = isAdmin();
$base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '/sulamprojectex';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$username = $_SESSION['username'] ?? 'User';
$userRole = $isAdmin ? 'Admin' : 'Resident';
?>
<aside class="sidebar">
  <!-- Brand Section -->
  <div class="brand">
    <div class="brand-text">
      <i class="fa-solid fa-mosque"></i> masjidkamek
    </div>
    <div class="logo-container">
      <img src="<?php echo $base; ?>/assets/uploads/masjid_logo.jpg" alt="Masjid Logo" class="sidebar-logo">
    </div>
  </div>
  
  <!-- Navigation -->
  <nav class="nav">
    <a href="<?php echo $base; ?>/dashboard" class="<?php echo str_starts_with($path, "$base/dashboard") ? 'active' : ''; ?>">
      <i class="fas fa-home"></i> Dashboard
    </a>
    <?php if ($isAdmin): ?>
    <a href="<?php echo $base; ?>/users" class="<?php echo str_starts_with($path, "$base/users") ? 'active' : ''; ?>">
      <i class="fas fa-users-cog"></i> User Management
    </a>
    <a href="<?php echo $base; ?>/families" class="<?php echo str_starts_with($path, "$base/families") ? 'active' : ''; ?>">
      <i class="fas fa-house-user"></i> Families
    </a>
    <a href="<?php echo $base; ?>/financial" class="<?php echo str_starts_with($path, "$base/financial") ? 'active' : ''; ?>">
      <i class="fas fa-file-invoice-dollar"></i> Financial
    </a>

    <?php endif; ?>
    <a href="<?php echo $base; ?>/donations" class="<?php echo str_starts_with($path, "$base/donations") ? 'active' : ''; ?>">
      <i class="fas fa-hand-holding-heart"></i> Donations
    </a>
    <a href="<?php echo $base; ?>/events" class="<?php echo str_starts_with($path, "$base/events") ? 'active' : ''; ?>">
      <i class="fas fa-calendar-alt"></i> Events
    </a>
    <a href="<?php echo $base; ?>/death-funeral" class="<?php echo str_starts_with($path, "$base/death-funeral") ? 'active' : ''; ?>">
      <i class="fas fa-dove"></i> Death Funeral
    </a>

    
    <!-- User Profile Section -->
    <div class="nav-divider">
      <a href="<?php echo $base; ?>/profile" class="user-profile <?php echo str_starts_with($path, "$base/profile") ? 'active' : ''; ?>">
        <div class="default-view">
          <i class="fas fa-user-circle"></i>
          <div>
            <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
            <div class="user-role"><?php echo htmlspecialchars($userRole); ?></div>
          </div>
        </div>
        <div class="hover-view">
          <i class="fas fa-pen"></i> Edit Profile
        </div>
      </a>
      
      <!-- Settings & Logout Side by Side -->
      <div class="sidebar-actions">
        <a href="<?php echo $base; ?>/settings">
          <i class="fas fa-cog"></i> Settings
        </a>
        <a href="<?php echo $base; ?>/logout">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </div>
  </nav>
  
  <!-- Sidebar Footer -->
  <div class="sidebar-footer">
    
    <!-- Contact Us Section -->
    <div class="sidebar-contact">
      <div class="sidebar-contact-title">Contact Us</div>
      <div class="sidebar-contact-item">
        <i class="fa-solid fa-location-dot"></i>
        <span>Taman Desa Ilmu<br>94300 Kota Samarahan, Sarawak</span>
      </div>
      <div class="sidebar-contact-item">
        <i class="fa-solid fa-envelope"></i>
        <a href="mailto:jkpmtdi@gmail.com">jkpmtdi@gmail.com</a>
      </div>
      <div class="sidebar-contact-item">
        <i class="fa-solid fa-phone"></i>
        <a href="tel:+60123456789">+60 12-345 6789</a>
      </div>
    </div>
    
    <!-- Social Media Links -->
    <div class="sidebar-social">
      <a href="https://www.facebook.com/MTDI94300/" class="sidebar-social-link" aria-label="Facebook" title="Facebook">
        <i class="fa-brands fa-facebook"></i>
      </a>
      <a href="https://chat.whatsapp.com/D589hP73ciZKgNPuDr1qR8" class="sidebar-social-link" aria-label="WhatsApp" title="WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
      </a>
      <a href="#" class="sidebar-social-link" aria-label="Instagram" title="Instagram">
        <i class="fa-brands fa-instagram"></i>
      </a>
      <a href="#" class="sidebar-social-link" aria-label="Telegram" title="Telegram">
        <i class="fa-brands fa-telegram"></i>
      </a>
    </div>
    
    <!-- Copyright -->
    <div class="sidebar-copyright">
      <small>&copy; <?php echo date('Y'); ?> masjidkamek</small>
    </div>
  </div>
</aside>
