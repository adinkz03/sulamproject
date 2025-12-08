# Setup Guide for SulamProject

This guide will help you set up and run SulamProject on your local development environment. We'll cover setup for both **Laragon** and **XAMPP**, the two most popular PHP development environments on Windows.

## What This Application Does

SulamProject is a PHP web application that uses **clean URLs** and **routing**. This means:
- Instead of URLs like `login.php` or `register.php`
- You get clean URLs like `/login` or `/register`
- All requests go through a single entry point (`index.php`)
- The router decides which page to show based on the URL

## Prerequisites

Before you start, make sure you have:
- ✅ A local web server (Laragon, XAMPP, or similar)
- ✅ PHP 7.4 or higher
- ✅ MySQL/MariaDB database server
- ✅ Apache with `mod_rewrite` enabled

---

## Option 1: Setup with Laragon (Recommended)

### Step 1: Install and Start Laragon

1. Download Laragon from [laragon.org](https://laragon.org)
2. Install it (default location: `C:\laragon`)
3. Launch Laragon
4. Click **"Start All"** to start Apache and MySQL

### Step 2: Place Your Project

1. Copy the `sulamproject` folder to `C:\laragon\www\`
2. Your project should be at: `C:\laragon\www\sulamproject\`

### Step 3: Verify `.htaccess` File Exists

Check if there's a file named `.htaccess` in your project root (`C:\laragon\www\sulamproject\.htaccess`).

If it doesn't exist, create it with this content:

```apache
# Enable rewrite engine
RewriteEngine On

# Set the base directory (adjust if your app is in a subdirectory)
RewriteBase /sulamproject/

# If the request is for an actual file or directory, serve it directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Otherwise, route all requests through index.php
RewriteRule ^(.*)$ index.php [QSA,L]
```

**What does this do?**
- `RewriteEngine On` - Turns on URL rewriting
- `RewriteBase /sulamproject/` - Sets the base URL path for your app
- `RewriteCond` lines - Only rewrite if the URL is NOT an actual file or folder
- `RewriteRule` - Send everything else to `index.php`

### Step 4: Access Your Application

Open your browser and go to:
```
http://localhost/sulamproject/login
```

**Or use the pretty domain** (already configured by Laragon):
```
http://sulamproject.test/login
```

### Step 5: Database Setup

**Recommended**: Import the SQL files manually for a complete setup.

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Import the SQL files in this order:
   - First: `database/schema.sql` (creates database and tables)
   - Then: `database/dump.sql` (optional - sample data)
   - Finally: All files in `database/migrations/` folder in numerical order

**Alternative**: The database can auto-provision basic tables when you first visit the registration page, but manual import is recommended for the complete schema.

**Database name**: `masjidkamek`

---

## Option 2: Setup with XAMPP

### Step 1: Install and Start XAMPP

1. Download XAMPP from [apachefriends.org](https://www.apachefriends.org)
2. Install it (default location: `C:\xampp`)
3. Launch XAMPP Control Panel
4. Start **Apache** and **MySQL** modules

### Step 2: Enable mod_rewrite (Important!)

XAMPP sometimes has `mod_rewrite` disabled by default.

1. Open `C:\xampp\apache\conf\httpd.conf` in a text editor
2. Find this line (around line 150-200):
   ```apache
   #LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Remove the `#` to uncomment it:
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Save the file

### Step 3: Enable .htaccess Files

1. In the same `httpd.conf` file, find the section for your web directory (around line 220-250):
   ```apache
   <Directory "C:/xampp/htdocs">
       ...
       AllowOverride None
       ...
   </Directory>
   ```

2. Change `AllowOverride None` to `AllowOverride All`:
   ```apache
   <Directory "C:/xampp/htdocs">
       ...
       AllowOverride All
       ...
   </Directory>
   ```

3. Save the file

### Step 4: Restart Apache

In XAMPP Control Panel:
1. Click **"Stop"** next to Apache
2. Wait 2 seconds
3. Click **"Start"** next to Apache

### Step 5: Place Your Project

1. Copy the `sulamproject` folder to `C:\xampp\htdocs\`
2. Your project should be at: `C:\xampp\htdocs\sulamproject\`

### Step 6: Create .htaccess File

Create a file named `.htaccess` in `C:\xampp\htdocs\sulamproject\.htaccess` with this content:

```apache
# Enable rewrite engine
RewriteEngine On

# Set the base directory
RewriteBase /sulamproject/

# Don't rewrite if it's an actual file or directory
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Route everything else through index.php
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Step 7: Update Database Configuration

1. The database configuration is in `features/shared/lib/database/mysqli-db.php` and `features/shared/lib/database/Database.php`
2. Default credentials (XAMPP defaults):
   ```php
   $host = 'localhost';
   $username = 'root';
   $password = '';  // XAMPP has no password by default
   $database = 'masjidkamek';
   ```

### Step 8: Access Your Application

Open your browser and go to:
```
http://localhost/sulamproject/login
```

### Step 9: Database Setup

**Recommended**: Import SQL files manually via phpMyAdmin:

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Import these files in order:
   - `database/schema.sql` (creates database `masjidkamek` and tables)
   - `database/dump.sql` (optional sample data)
   - All files in `database/migrations/` folder (001 till the end)

**Alternative**: Visit `/register` for basic auto-provisioning (not recommended)

---

## Option 3: Generic Apache Server

If you're using a different Apache setup (WAMP, custom installation, Linux, etc.):

### Requirements Check

1. **Verify mod_rewrite is enabled:**
   ```bash
   # Linux/Mac
   apache2 -M | grep rewrite
   
   # Or check in Apache config
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

2. **Verify AllowOverride is enabled:**
   Look in your Apache config (`httpd.conf` or site config) for:
   ```apache
   <Directory "/path/to/your/webroot">
       AllowOverride All
   </Directory>
   ```

### Setup Steps

1. **Place project in your web root**
   - Apache default: `/var/www/html/` (Linux) or `C:\Apache24\htdocs\` (Windows)
   - Example: `/var/www/html/sulamproject/`

2. **Create .htaccess file** in project root:
   ```apache
   RewriteEngine On
   RewriteBase /sulamproject/
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php [QSA,L]
   ```

3. **Update RewriteBase if needed:**
   - If your app is at `http://localhost/sulamproject/`, use `/sulamproject/`
   - If your app is at `http://localhost/`, use `/`
   - If your app is at `http://localhost/apps/sulamproject/`, use `/apps/sulamproject/`

4. **Restart Apache:**
   ```bash
   # Linux
   sudo systemctl restart apache2
   # or
   sudo service apache2 restart
   
   # Windows (if running as service)
   net stop Apache2.4
   net start Apache2.4
   ```

---

## Troubleshooting Common Issues

### Issue 1: "404 Not Found" when accessing /login

**Problem:** Apache can't find the page because URL rewriting isn't working.

**Solutions:**
1. ✅ Check if `.htaccess` file exists in project root
2. ✅ Verify `mod_rewrite` is enabled in Apache config
3. ✅ Verify `AllowOverride All` is set for your web directory
4. ✅ Restart Apache after making config changes
5. ✅ Check that `RewriteBase` matches your URL structure

### Issue 2: "500 Internal Server Error"

**Problem:** Apache found the `.htaccess` file but something is wrong with it.

**Solutions:**
1. ✅ Check Apache error log:
   - Laragon: `C:\laragon\bin\apache\logs\error.log`
   - XAMPP: `C:\xampp\apache\logs\error.log`
2. ✅ Verify `mod_rewrite` is enabled
3. ✅ Check `.htaccess` syntax for typos
4. ✅ Make sure there are no BOM (Byte Order Mark) characters in `.htaccess`

### Issue 3: CSS/JS/Images Not Loading

**Problem:** The rewrite rules are catching asset files too.

**Solution:** The `.htaccess` file already handles this with:
```apache
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
```

These lines tell Apache: "Don't rewrite if the file or directory actually exists."

Make sure your assets are in:
- `assets/css/`
- `assets/js/`
- `assets/uploads/`

### Issue 4: Database Connection Errors

**Solutions:**
1. ✅ Check MySQL is running
2. ✅ Verify credentials in `db.php`:
   - Laragon default: `root` with blank password
   - XAMPP default: `root` with blank password
3. ✅ Visit `/register` to auto-provision the database
4. ✅ Check phpMyAdmin to verify database was created

### Issue 5: "Access Denied" or Permission Errors

**Solutions (Windows):**
1. ✅ Run your web server as Administrator
2. ✅ Check folder permissions on project directory

**Solutions (Linux):**
```bash
# Give Apache permission to read files
sudo chown -R www-data:www-data /var/www/html/sulamproject
sudo chmod -R 755 /var/www/html/sulamproject
```

---

## Understanding the Architecture

### How Routing Works

```
User visits: http://localhost/sulamproject/login
       ↓
Apache receives request for "/login"
       ↓
.htaccess checks: Is "login" a real file? NO
       ↓
.htaccess checks: Is "login" a real folder? NO
       ↓
.htaccess redirects to: index.php (with original URL preserved)
       ↓
index.php loads Router from features/shared/lib/routes.php
       ↓
Router matches "/login" to AuthController->showLogin()
       ↓
Controller renders the login page
       ↓
User sees the login form!
```

### Project Structure

```
sulamproject/
├── .htaccess              ← Apache rewrite rules (YOU NEED THIS!)
├── index.php              ← Front controller (entry point)
├── db.php                 ← Database connection
├── features/              ← All application features
│   ├── shared/
│   │   └── lib/
│   │       └── routes.php ← URL routing definitions
│   ├── users/             ← Login, register, etc.
│   ├── dashboard/         ← Dashboard pages
│   ├── residents/         ← Resident management
│   └── ...
└── assets/                ← CSS, JS, images
```

### Key Files Explained

**`.htaccess`** - Tells Apache how to handle URLs
- Lives in project root
- Controls URL rewriting
- Routes all requests through `index.php`

**`index.php`** - Front controller
- Entry point for ALL requests
- Loads the router
- Dispatches requests to correct controllers

**`features/shared/lib/routes.php`** - Route definitions
- Maps URLs like `/login` to controllers
- Defines which page handles which URL

---

## Testing Your Setup

### Quick Test Checklist

Test these URLs in your browser:

1. ✅ `http://localhost/sulamproject/` → Should redirect to login
2. ✅ `http://localhost/sulamproject/login` → Should show login page
3. ✅ `http://localhost/sulamproject/register` → Should show registration
4. ✅ `http://localhost/sulamproject/assets/css/style.css` → Should show CSS file

If all four work, your setup is correct! 🎉

### Check Apache Error Logs

If something goes wrong, check the logs:

**Laragon:**
```
C:\laragon\bin\apache\apache-2.4.54-win64-VS16\logs\error.log
```

**XAMPP:**
```
C:\xampp\apache\logs\error.log
```

**Linux:**
```bash
sudo tail -f /var/log/apache2/error.log
```

---

## Next Steps

After setup is complete:

1. **Create your first admin user** at `/register`
2. **Login** at `/login`
3. **Access the dashboard** at `/dashboard`

For more information:
- See `AGENTS.md` for development guidelines
- See `context-docs/` for detailed architecture documentation
- See `README.md` for project overview

---

## Need Help?

Common questions:

**Q: Do I need to create the database manually?**  
A: No! Just visit `/register` and it auto-creates everything.

**Q: Can I use a different folder name?**  
A: Yes, but update `RewriteBase` in `.htaccess` to match your folder name.

**Q: Do I need Composer or npm?**  
A: Optional. The PHP app works standalone. npm is only for asset bundling with Vite.

**Q: What PHP version do I need?**  
A: PHP 7.4 or higher. PHP 8.x is recommended.

**Q: Why can't I access the site without .htaccess?**  
A: Because the app uses routing. Without `.htaccess`, Apache doesn't know to send requests to `index.php`.
