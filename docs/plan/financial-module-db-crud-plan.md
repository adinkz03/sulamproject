# Financial Module Database & CRUD Plan

## Overview
This plan tracks the work to move the **Financial** module (Akaun Bayaran & Akaun Terimaan) from placeholder data to fully database-backed CRUD, aligned with SulamProject conventions.

Scope:
- Design and create two tables in the `masjidkamek` database
- Implement PHP data access layer using `mysqli` prepared statements
- Wire listing + create + update + delete into existing financial pages
- Keep routing, layouts, and RBAC consistent with existing modules

---

## 1. Database Schema Design

### 1.1 Tables
- `financial_payment_accounts`
- `financial_deposit_accounts`

### 1.2 Common Columns
For both tables:
- `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `tx_date` DATE NOT NULL
- `description` VARCHAR(255) NOT NULL
- `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP

### 1.3 Akaun Bayaran (Payments)
Table: `financial_payment_accounts`

Category columns (DECIMAL(12,2) UNSIGNED, default 0.00):
- `perayaan_islam`
- `pengimarahan_aktiviti_masjid`
- `penyelenggaraan_masjid`
- `keperluan_kelengkapan_masjid`
- `gaji_upah_saguhati_elaun`
- `sumbangan_derma`
- `mesyuarat_jamuan`
- `utiliti`
- `alat_tulis_percetakan`
- `pengangkutan_perjalanan`
- `caj_bank`
- `lain_lain_perbelanjaan`

### 1.4 Akaun Terimaan (Deposits)
Table: `financial_deposit_accounts`

Category columns (DECIMAL(12,2) UNSIGNED, default 0.00):
- `geran_kerajaan`
- `sumbangan_derma`
- `tabung_masjid`
- `kutipan_jumaat_sadak`
- `kutipan_aidilfitri_aidiladha`
- `sewa_peralatan_masjid`
- `hibah_faedah_bank`
- `faedah_simpanan_tetap`
- `sewa_rumah_kedai_tadika_menara`
- `lain_lain_terimaan`

### 1.5 SQL DDL

**Migration file**: `database/migrations/006_create_financial_tables.sql`

```sql
-- Table for Payment Accounts (Akaun Bayaran)
CREATE TABLE IF NOT EXISTS `financial_payment_accounts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tx_date` DATE NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `perayaan_islam` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `pengimarahan_aktiviti_masjid` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `penyelenggaraan_masjid` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `keperluan_kelengkapan_masjid` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `gaji_upah_saguhati_elaun` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `sumbangan_derma` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `mesyuarat_jamuan` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `utiliti` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `alat_tulis_percetakan` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `pengangkutan_perjalanan` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `caj_bank` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `lain_lain_perbelanjaan` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_tx_date` (`tx_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Deposit Accounts (Akaun Terimaan)
CREATE TABLE IF NOT EXISTS `financial_deposit_accounts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tx_date` DATE NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `geran_kerajaan` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `sumbangan_derma` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `tabung_masjid` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `kutipan_jumaat_sadak` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `kutipan_aidilfitri_aidiladha` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `sewa_peralatan_masjid` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `hibah_faedah_bank` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `faedah_simpanan_tetap` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `sewa_rumah_kedai_tadika_menara` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `lain_lain_terimaan` DECIMAL(12,2) UNSIGNED DEFAULT 0.00,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_tx_date` (`tx_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Status**: ✅ COMPLETED - Tables created in `masjidkamek` database

---

## 2. Data Access Layer (Models)

Location: `features/financial/shared/lib/`

### 2.1 PaymentAccountRepository
Responsibilities:
- `findAll()` – list all payment rows (latest first)
- `findById($id)` – fetch single row
- `create(array $data)` – insert new row
- `update($id, array $data)` – update existing row
- `delete($id)` – delete row by id

Implementation notes:
- Use injected `$mysqli` from `mysqli-db.php`
- Use prepared statements for all queries
- Treat all category fields as numeric, default 0.00 if empty

### 2.2 DepositAccountRepository
Same responsibilities and style as `PaymentAccountRepository`, but for `financial_deposit_accounts`.

---

## 3. Controller & Route Integration

Location: `features/financial/admin/controllers/FinancialController.php`, `features/shared/lib/routes.php`.

### 3.1 Listing Pages
- Update `paymentAccount()` and `depositAccount()` to:
  - Call respective repositories `findAll()`
  - Pass records array to the views (`$payments`, `$deposits`)
- Remove hardcoded rows from `payment-account.php` and `deposit-account.php` views; loop over DB data.

### 3.2 Create (Add) Flows
- Routes:
  - `GET /financial/payment-account/add` – show payment add form (already scaffolded)
  - `POST /financial/payment-account/add` – handle create, then redirect back to listing
  - `GET /financial/deposit-account/add` – show deposit add form (already scaffolded)
  - `POST /financial/deposit-account/add` – handle create, then redirect back to listing
- Controller methods:
  - `storePayment()` and `storeDeposit()` for POST handling.
- Validation:
  - Require `tx_date`, `description`, at least one numeric field > 0

### 3.3 Edit & Delete
- Routes (admin-only):
  - `GET /financial/payment-account/edit?id=...`
  - `POST /financial/payment-account/edit?id=...`
  - `POST /financial/payment-account/delete` (with CSRF later)
  - Mirror for `/financial/deposit-account/...`
- Controller methods:
  - `editPayment()`, `updatePayment()`, `deletePayment()`
  - `editDeposit()`, `updateDeposit()`, `deleteDeposit()`
- Views:
  - Reuse existing add-form views with prefilled values where possible.

---

## 4. View Updates

Location: `features/financial/admin/views/`

### 4.1 Listing Views
- `payment-account.php`
  - Replace hardcoded rows with `foreach ($payments as $row)`
  - Add action column with Edit/Delete buttons.
- `deposit-account.php`
  - Same pattern with `$deposits`.

### 4.2 Add/Edit Views
- `payment-add.php`, `deposit-add.php`
  - On POST error, re-display form with old values and simple error messages.
  - When used in edit mode, populate from existing DB row.

---

## 5. Execution & Testing Notes

### 5.1 Running SQL
- Use phpMyAdmin or CLI against `masjid` database to apply the `CREATE TABLE` commands from Section 1.5.

Example CLI pattern (adjust credentials as needed):
```bash
mysql -u root -p masjid
-- then paste CREATE TABLE statements
```

### 5.2 Manual Test Checklist
- [ ] Visit `/financial` as admin – page loads.
- [ ] Visit `/financial/payment-account` – table shows with 0 rows.
- [ ] Add payment via `/financial/payment-account/add` – row appears in listing.
- [ ] Edit payment – changes persist.
- [ ] Delete payment – row removed.
- [ ] Repeat analogous checks for `/financial/deposit-account`.

---

## 6. Open Questions / Next Iterations
- Add CSRF tokens to add/edit/delete forms per security guidelines.
- Add basic flash messaging for success/error states.
- Consider simple filtering by date range or category.
