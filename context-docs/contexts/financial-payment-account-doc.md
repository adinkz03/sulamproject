**Financial - Payment Account (Akaun Bayaran) Documentation**

Purpose
- **What:** Admin page for listing, creating, editing and deleting payment (bayaran) records.
- **Where:** `GET /financial/payment-account` (see router).
- **Who:** Admin-only (routes call `requireAdmin()`).

Quick paths (primary files)
- **Route config:** `features/shared/lib/routes.php` (contains `/financial/payment-account` and related add/edit/delete routes)
- **Page loader:** `features/financial/admin/pages/payment-account.php`
- **Controller:** `features/financial/admin/controllers/FinancialController.php` (method: `paymentAccount()`)
- **Repository / Model:** `features/financial/shared/lib/PaymentAccountRepository.php`
- **View (listing):** `features/financial/admin/views/payment-account.php`
- **Add/Edit page wrappers:** `features/financial/admin/pages/payment-add.php`, `features/financial/admin/pages/payment-edit.php`
- **Add/Edit view (form):** `features/financial/admin/views/payment-add.php`
- **Delete handler:** `features/financial/admin/ajax/payment-delete.php` (POST)
- **CSS for page:** `features/financial/admin/assets/css/financial.css`
- **Layouts:** `features/shared/components/layouts/app-layout.php`, `features/shared/components/layouts/base.php`

Routing and access
- The route is registered in `features/shared/lib/routes.php`:
  - `GET /financial/payment-account` → loads `payment-account.php` after `initSecureSession()`, `requireAuth()`, `requireAdmin()`.
  - Add/Edit/Delete routes for payments are also registered (see `/financial/payment-account/add`, `/edit`, `/delete`).
- Admin-only: the page calls `requireAdmin()` via the router, so a junior dev should not change page access checks unless intentionally altering permissions.

Page loader (`payment-account.php`)
- Purpose: bootstrap common dependencies, instantiate `FinancialController`, call `paymentAccount()` and render view inside app layout.
- Key imports:
  - `features/shared/lib/auth/session.php` — session helpers like `initSecureSession()` and role checks.
  - `features/shared/lib/utilities/functions.php` — URL helpers (`url()`), escaping helpers (`e()`/`htmlspecialchars()`), and redirect helpers.
  - `features/shared/lib/database/mysqli-db.php` — supplies `$mysqli` instance.
  - `features/financial/admin/controllers/FinancialController.php` — business logic.
- Flow:
  1. `initSecureSession(); requireAuth();` ensures logged-in user.
 2. Instantiate controller: `$controller = new FinancialController($mysqli);`.
 3. `$data = $controller->paymentAccount(); extract($data);` — the controller returns an associative array used by the view.
 4. Prepare `$pageHeader` with title/actions (used by page header component).
 5. Include view `../views/payment-account.php` into `$content` then wrap using `app-layout.php` + `base.php`.

Controller (`FinancialController::paymentAccount()`)
- Location: `features/financial/admin/controllers/FinancialController.php`.
- What it returns:
  - `payments` — array of rows from `PaymentAccountRepository::findAll()`.
  - `categoryColumns` — constant `PaymentAccountRepository::CATEGORY_COLUMNS` (ordered list of category column names).
  - `categoryLabels` — `PaymentAccountRepository::CATEGORY_LABELS` (mapping of column name → human label).
  - `totalCash`, `totalBank` — sums split by `payment_method`.
- Key logic:
  - Iterates `findAll()` results and uses `PaymentAccountRepository::calculateRowTotal($row)` to calculate each row's total.
  - Totals are aggregated into `totalCash` or `totalBank` depending on `$row['payment_method']`.
- Validation and other methods (create/update/delete) live in the same controller and are used by the add/edit pages (see `storePayment`, `updatePayment`, `deletePayment`).

Repository (`PaymentAccountRepository`)
- File: `features/financial/shared/lib/PaymentAccountRepository.php`.
- Responsibilities: CRUD operations on `financial_payment_accounts` table using `mysqli` and prepared statements.
- Important constants:
  - `CATEGORY_COLUMNS` — a PHP array of column names that represent the expense categories (e.g. `perayaan_islam`, `utiliti`, `lain_lain_perbelanjaan`, ...). These columns are the dynamic part for rendering columns and summing totals.
  - `CATEGORY_LABELS` — associative array mapping column name → label shown in the UI.
- Key methods:
  - `findAll()` — returns all payment rows ordered by `tx_date DESC, id DESC`.
  - `findById($id)` — fetch single row.
  - `create($data)` — builds an INSERT with common columns + each category column; uses `sanitizeAmount()` to coerce amounts.
  - `update($id, $data)` — builds UPDATE SET for standard fields + categories.
  - `delete($id)` — delete by id.
  - `calculateRowTotal($row)` — adds up all category columns in a row to get the row total.
- Where to change categories:
  - To add/remove a category column you must: (1) update the database schema (migration in `database/migrations/`), (2) adjust `CATEGORY_COLUMNS` and `CATEGORY_LABELS` here, (3) update any UI or reports that assume column widths (CSS table min-widths), (4) run DB migration locally.

Listing View (`payment-account.php` view)
- File: `features/financial/admin/views/payment-account.php`.
- Expected variables: `$payments`, `$categoryColumns`, `$categoryLabels`, `$totalCash`, `$totalBank` (controller injects them).
- UI pieces:
  - Stat cards: top summary showing total cash, bank and grand total.
  - If no rows: an empty state message with a link to `financial/payment-account/add`.
  - Table: horizontally-scrollable (`.table-responsive--wide`) table with a sticky left column for description.
  - Columns: fixed columns (date, voucher no, description, payment method), then one column per category (labels come from `CATEGORY_LABELS`), then row total and actions.
  - Actions per row: print voucher (`financial/voucher-print?id=<id>`), edit (`financial/payment-account/edit?id=<id>`), delete (POST to `financial/payment-account/delete`). Delete uses a form POST protected by `onsubmit` confirm.
- Formatting:
  - Amounts are displayed via `formatAmount()` helper inside the view (simple `RM number_format(..., 2)` or `-` for zero).
  - CSS file linked at the bottom: `features/financial/admin/assets/css/financial.css`.

Add / Edit Form
- Pages:
  - `features/financial/admin/pages/payment-add.php` — shows add form and handles POST by calling `$controller->storePayment($_POST)`.
  - `features/financial/admin/pages/payment-edit.php` — gets `id` from query, loads record via controller, handles POST to `updatePayment`.
- View: `features/financial/admin/views/payment-add.php` (used for both add and edit).
  - Fields included:
    - `voucher_number` (optional)
    - `tx_date` (required)
    - `paid_to` (required)
    - `payee_ic`, `payee_bank_name`, `payee_bank_account` (optional)
    - `description` (required)
    - `payment_method` (required, select: `cash` or `bank`)
    - `payment_reference` (optional; note doc text says required for bank transfers but validation is separate)
    - One numeric input per category column (`CATEGORY_COLUMNS`) (min=0, step=0.01). At least one category must be > 0.
- Validation rules (server-side in `FinancialController::validatePaymentData`):
  - `tx_date` required
  - `description` required
  - `paid_to` required
  - `payment_method` required
  - At least one category column must contain a positive numeric value
  - Client-side the form uses `required` attributes where appropriate, but server-side validation is authoritative.

Delete handler
- `features/financial/admin/ajax/payment-delete.php` accepts only POST and uses the controller's `deletePayment()`.
- The table view uses a simple HTML form POST to call deletion; the handler redirects back to listing after deletion.

CSS & Layout
- `features/financial/admin/assets/css/financial.css` imports shared styles and contains table modifiers:
  - `.table--payment-account` sets `min-width` (2200px) and padding/nowrap to accommodate many category columns.
  - `.sticky-col-left` manages the sticky description column.
- To change styling:
  - Edit `financial.css` for page-specific tweaks.
  - Shared styles live under `features/shared/assets/css/` (variables, base.css, etc.).

How data maps to DB
- Table names used by repository code:
  - `financial_payment_accounts` — payments
  - `financial_deposit_accounts` — deposits (see deposit repo doc)
- Column groups:
  - Standard columns: `id`, `tx_date`, `voucher_number`, `paid_to`, `payee_ic`, `payee_bank_name`, `payee_bank_account`, `payment_method`, `payment_reference`, plus all category columns defined in `CATEGORY_COLUMNS`.
- If you need to inspect or change the schema, check `database/migrations/` and `database/schema.sql` for migration examples.

Common maintenance tasks (junior dev checklist)
- Add a new category column:
  1. Add column in DB migration (`database/migrations/XXXX_add_new_category.sql`).
  2. Add the column name to `PaymentAccountRepository::CATEGORY_COLUMNS` (preserve order).
  3. Add label in `PaymentAccountRepository::CATEGORY_LABELS`.
  4. Update any CSS min-width in `financial.css` if table grows too wide.
  5. Test: Add a payment and ensure sums and listing show the new column.
- Change validation (e.g., require `payment_reference` for bank): modify `FinancialController::validatePaymentData()`.
- Change routes or access: update `features/shared/lib/routes.php` but be careful — router centrally enforces `requireAdmin()`.

Related files (quick links)
- Router: `features/shared/lib/routes.php`
- Pages: `features/financial/admin/pages/payment-account.php`, `payment-add.php`, `payment-edit.php`
- Views: `features/financial/admin/views/payment-account.php`, `payment-add.php`
- Controller: `features/financial/admin/controllers/FinancialController.php`
- Repo: `features/financial/shared/lib/PaymentAccountRepository.php`
- Delete handler: `features/financial/admin/ajax/payment-delete.php`
- Styles: `features/financial/admin/assets/css/financial.css`
- Layouts: `features/shared/components/layouts/app-layout.php`, `features/shared/components/layouts/base.php`

If you want me to:
- Run a quick search to list all database columns for `financial_payment_accounts` (I'll look at `database/schema.sql`) or generate a migration example, tell me and I will prepare it.

---

**End of Payment Account documentation**

Print Pages (Voucher)
- Files involved:
  - `features/financial/admin/pages/voucher-print.php` — the payment voucher print template and logic.
  - Route: `/financial/voucher-print` registered in `features/shared/lib/routes.php` (router enforces `initSecureSession()`, `requireAuth()`, `requireAdmin()` before loading the page).
- What it does:
  - Reads `id` from `$_GET['id']` and validates it.
  - Loads the payment row using `PaymentAccountRepository::findById($id)`.
  - Computes `totalAmount` by summing all columns in `PaymentAccountRepository::CATEGORY_COLUMNS` and builds a `categories` list of non-zero category entries for display.
  - Formats date and converts numeric amount to words via `numberToWords()` (utility helper available in shared utilities).
  - Outputs a self-contained HTML document with embedded CSS optimized for printing (A4) and a small `window.print()` script triggered on load.
  - Provides on-screen buttons (`.no-print`) for manual print/close; these are hidden when printing via `@media print` rules.
- Where to change content/style:
  - Header text and contact address are in the template HTML inside `voucher-print.php` — edit those strings to match your mosque details.
  - Signature placeholders and labels are static HTML — modify layout, label text or the number of approval slots directly in the file.
  - Transaction rows are generated from `PaymentAccountRepository::CATEGORY_COLUMNS` and `CATEGORY_LABELS` — to change which categories appear, update the repository constants and DB schema (see earlier section).
  - The auto-print behavior is a small JS block at the end of the file; remove or comment it out if you don't want automatic print on load.
- Security and behaviour notes:
  - The route is admin-only (enforced by the router). The template itself calls `initSecureSession()` and `requireAuth()` too.
  - Input `id` is cast to `(int)`; however, the template directly dies on invalid ID — you can change that to a nicer error page or redirect if desired.

