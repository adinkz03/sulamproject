<?php
// Site footer with contact info and quick links
?>
<footer class="site-footer">
  <div class="footer-container">
    <div class="footer-grid">
      
      <!-- About Section -->
      <div class="footer-section">
        <h4 class="footer-heading">
          <i class="fa-solid fa-mosque"></i> OurMasjid
        </h4>
        <p class="footer-description">
          Serving the Muslim community with compassion and dedication. Managing zakat, donations, and community programs.
        </p>
      </div>
      
      <!-- Quick Links -->
      <div class="footer-section">
        <h4 class="footer-heading">Quick Links</h4>
        <ul class="footer-links">
          <li><a href="<?php echo url('/'); ?>"><i class="fa-solid fa-home"></i> Dashboard</a></li>
          <li><a href="<?php echo url('features/residents/admin/pages/residents.php'); ?>"><i class="fa-solid fa-users"></i> Residents</a></li>
          <li><a href="<?php echo url('features/donations/admin/pages/donations.php'); ?>"><i class="fa-solid fa-hand-holding-heart"></i> Donations</a></li>
          <li><a href="<?php echo url('features/events/admin/pages/events.php'); ?>"><i class="fa-solid fa-calendar-alt"></i> Events</a></li>
        </ul>
      </div>
      
      <!-- Contact Info -->
      <div class="footer-section">
        <h4 class="footer-heading">Contact Us</h4>
        <ul class="footer-contact">
          <li>
            <i class="fa-solid fa-location-dot"></i>
            <span>Taman Desa Ilmu<br>94300 Kota Samarahan, Sarawak</span>
          </li>
          <li>
            <i class="fa-solid fa-envelope"></i>
            <a href="mailto:jkpmtdi@gmail.com">jkpmtdi@gmail.com</a>
          </li>
          <li>
            <i class="fa-solid fa-phone"></i>
            <a href="tel:+60123456789">+60 12-345 6789</a>
          </li>
        </ul>
      </div>
      
      <!-- Social & Copyright -->
      <div class="footer-section">
        <h4 class="footer-heading">Connect With Us</h4>
        <div class="footer-social">
          <a href="#" class="social-link" aria-label="Facebook" title="Facebook">
            <i class="fa-brands fa-facebook"></i>
          </a>
          <a href="https://chat.whatsapp.com/D589hP73ciZKgNPuDr1qR8" class="social-link" aria-label="WhatsApp" title="WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
          </a>
          <a href="#" class="social-link" aria-label="Instagram" title="Instagram">
            <i class="fa-brands fa-instagram"></i>
          </a>
          <a href="#" class="social-link" aria-label="Telegram" title="Telegram">
            <i class="fa-brands fa-telegram"></i>
          </a>
        </div>
      </div>
      
    </div>
    
    <!-- Bottom Bar -->
    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> OurMasjid. All rights reserved.</p>
      <p class="footer-tagline">Built with <i class="fa-solid fa-heart"></i> for the community</p>
    </div>
  </div>
</footer>
