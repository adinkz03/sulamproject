# sulamproject
A secure, role-based system for Desa Ilmu that replaces Excel/myMasjid to manage residents, zakat aid, donations, death/funeral records, and events. Offers search, approvals, receipts, audit logs, reports, and backups—so no needy resident is overlooked.

## Registration Flow

The `register.php` page persists user accounts into a MySQL database named `masjid`, table `users`.

### Auto-provisioning
On first load (or first registration submit), the helper `db.php` will:
1. Connect to MySQL (`localhost`, user `root`, blank password — adjust if different).
2. Create the database `masjid` if it does not exist.
3. Select the database and create a `users` table if missing.

### `users` table structure
```sql
CREATE TABLE `users` (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(50) NOT NULL UNIQUE,
	email VARCHAR(120) NOT NULL UNIQUE,
	password_hash VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Security Notes
- Passwords are hashed with `password_hash()` (bcrypt/Argon depending on PHP build).
- All inserts use prepared statements to mitigate SQL injection.
- Basic validation enforces username length (3–50), email format/length, password min length (8).

### Local Setup (Laragon)
1. Start Apache & MySQL in the Laragon control panel.
2. Place the project in `c:\laragon\www\sulamcode\sulamproject`.
3. Visit `http://localhost/sulamcode/sulamproject/register.php` to create an account.
4. Check via phpMyAdmin (`http://localhost/phpmyadmin`) → database `masjid` → table `users`.

### Adjusting Credentials
Edit `db.php` if your MySQL root password differs or you want a dedicated user:
```php
$DB_HOST = 'localhost';
$DB_USER = 'app_user';
$DB_PASS = 'strong_password_here';
```
Create the user in MySQL:
```sql
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON masjid.* TO 'app_user'@'localhost';
FLUSH PRIVILEGES;
```

### Next Steps
- Add login verification using `password_verify()`.
- Implement session handling and logout.
- Introduce role column and permission checks.
- Add CSRF tokens to forms.
- Rate-limit registration to prevent abuse.

---
For questions or improvements, open an issue or propose a PR.
