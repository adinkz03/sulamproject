**Financial - Deposit Account (Akaun Terimaan) Documentation**

Purpose
- **What:** Admin page for listing, creating, editing and deleting deposit (terimaan) records.
- **Where:** `GET /financial/deposit-account` (see router).
- **Who:** Admin-only (routes call `requireAdmin()`).

Quick paths (primary files)
- **Route config:** `features/shared/lib/routes.php` (contains `/financial/deposit-account` and related add/edit/delete routes)
- **Page loader:** `features/financial/admin/pages/deposit-account.php`
- **Controller:** `features/financial/admin/controllers/FinancialController.php` (method: `depositAccount()`)
- **Repository / Model:** `features/financial/shared/lib/DepositAccountRepository.php`
- **View (listing):** `features/financial/admin/views/deposit-account.php`
- **Add/Edit page wrappers:** `features/financial/admin/pages/deposit-add.php`, `features/financial/admin/pages/deposit-edit.php`
- **Add/Edit view (form):** `features/financial/admin/views/deposit-add.php`
- **Delete handler:** `features/financial/admin/ajax/deposit-delete.php` (POST)
- **CSS for page:** `features/financial/admin/assets/css/financial.css`
- **Layouts:** `features/shared/components/layouts/app-layout.php`, `features/shared/components/layouts/base.php`

Routing and access
- The route is registered in `features/shared/lib/routes.php`:
  - `GET /financial/deposit-account` → loads `deposit-account.php` after `initSecureSession()`, `requireAuth()`, `requireAdmin()`.
  - Add/Edit/Delete routes for deposits are also registered (see `/financial/deposit-account/add`, `/edit`, `/delete`).
- Admin-only: the page calls `requireAdmin()` via the router.

Page loader (`deposit-account.php`)
- Purpose: bootstrap common dependencies, instantiate `FinancialController`, call `depositAccount()` and render view inside app layout.
- Key imports:
  - `features/shared/lib/auth/session.php` — session helpers like `initSecureSession()` and role checks.
  - `features/shared/lib/utilities/functions.php` — URL helpers (`url()`), escaping helpers (`e()`/`htmlspecialchars()`), and redirect helpers.
  - `features/shared/lib/database/mysqli-db.php` — supplies `$mysqli` instance.
  - `features/financial/admin/controllers/FinancialController.php` — business logic.
- Flow:
  1. `initSecureSession(); requireAuth();` ensures logged-in user.
 2. Instantiate controller: `$controller = new FinancialController($mysqli);`.
 3. `$data = $controller->depositAccount(); extract($data);` — the controller returns an associative array used by the view.
 4. Prepare `$pageHeader` with title/actions (used by page header component).
 5. Include view `../views/deposit-account.php` into `$content` then wrap using `app-layout.php` + `base.php`.

Controller (`FinancialController::depositAccount()`)
- Location: `features/financial/admin/controllers/FinancialController.php`.
- What it returns:
  - `deposits` — array of rows from `DepositAccountRepository::findAll()`.
  - `categoryColumns` — constant `DepositAccountRepository::CATEGORY_COLUMNS` (ordered list of category column names).
  - `categoryLabels` — `DepositAccountRepository::CATEGORY_LABELS` (mapping of column name → human label).
  - `totalCash`, `totalBank` — sums split by `payment_method`.
- Key logic:
  - Iterates `findAll()` results and uses `DepositAccountRepository::calculateRowTotal($row)` to calculate each row's total.
  - Totals are aggregated into `totalCash` or `totalBank` depending on `$row['payment_method']`.

Repository (`DepositAccountRepository`)
- File: `features/financial/shared/lib/DepositAccountRepository.php`.
- Responsibilities: CRUD operations on `financial_deposit_accounts` table using `mysqli` and prepared statements.
- Important constants:
  - `CATEGORY_COLUMNS` — a PHP array of column names that represent the receipt categories (e.g. `geran_kerajaan`, `sumbangan_derma`, `tabung_masjid`, ...).
  - `CATEGORY_LABELS` — associative array mapping column name → label shown in the UI.
- Key methods:
  - `findAll()` — returns all deposit rows ordered by `tx_date DESC, id DESC`.
  - `findById($id)` — fetch single row.
  - `create($data)` — builds an INSERT with common columns + each category column; uses `sanitizeAmount()` to coerce amounts.
  - `update($id, $data)` — builds UPDATE SET for standard fields + categories.
  - `delete($id)` — delete by id.
  - `calculateRowTotal($row)` — adds up all category columns in a row to get the row total.
- Where to change categories:
  - To add/remove a category column you must: (1) update the database schema (migration in `database/migrations/`), (2) adjust `CATEGORY_COLUMNS` and `CATEGORY_LABELS` here, (3) update any UI or reports that assume column widths (CSS table min-widths), (4) run DB migration locally.

Listing View (`deposit-account.php` view)
- File: `features/financial/admin/views/deposit-account.php`.
- Expected variables: `$deposits`, `$categoryColumns`, `$categoryLabels`, `$totalCash`, `$totalBank` (controller injects them).
- UI pieces:
  - Stat cards: top summary showing total cash, bank and grand total.
  - If no rows: an empty state message with a link to `financial/deposit-account/add`.
  - Table: horizontally-scrollable (`.table-responsive--wide`) table with a sticky left column for description.
  - Columns: fixed columns (date, receipt no, description, payment method), then one column per category (labels come from `CATEGORY_LABELS`), then row total and actions.
  - Actions per row: print receipt (`financial/receipt-print?id=<id>`), edit (`financial/deposit-account/edit?id=<id>`), delete (POST to `financial/deposit-account/delete`). Delete uses a form POST protected by `onsubmit` confirm.
- Formatting:
  - Amounts are displayed via `formatDepositAmount()` helper inside the view (simple `RM number_format(..., 2)` or `-` for zero).
  - CSS file linked at the bottom: `features/financial/admin/assets/css/financial.css`.

Add / Edit Form
- Pages:
  - `features/financial/admin/pages/deposit-add.php` — shows add form and handles POST by calling `$controller->storeDeposit($_POST)`.
  - `features/financial/admin/pages/deposit-edit.php` — gets `id` from query, loads record via controller, handles POST to `updateDeposit`.
- View: `features/financial/admin/views/deposit-add.php` (used for both add and edit).
  - Fields included:
    - `receipt_number` (optional)
    - `tx_date` (required)
    - `received_from` (required)
    - `description` (required)
    - `payment_method` (required, select: `cash` or `bank`)
    - `payment_reference` (optional; note doc text says required for bank transfers but validation is separate)
    - One numeric input per category column (`CATEGORY_COLUMNS`) (min=0, step=0.01). At least one category must be > 0.
- Validation rules (server-side in `FinancialController::validateDepositData`):
  - `tx_date` required
  - `description` required
  - `received_from` required
  - `payment_method` required
  - At least one category column must contain a positive numeric value
  - Client-side the form uses `required` attributes where appropriate, but server-side validation is authoritative.

Delete handler
- `features/financial/admin/ajax/deposit-delete.php` accepts only POST and uses the controller's `deleteDeposit()`.
- The table view uses a simple HTML form POST to call deletion; the handler redirects back to listing after deletion.

CSS & Layout
- `features/financial/admin/assets/css/financial.css` imports shared styles and contains table modifiers:
  - `.table--deposit-account` sets `min-width` (2000px) and padding/nowrap to accommodate many category columns.
  - `.sticky-col-left` manages the sticky description column.
- To change styling:
  - Edit `financial.css` for page-specific tweaks.
  - Shared styles live under `features/shared/assets/css/` (variables, base.css, etc.).

How data maps to DB
- Table names used by repository code:
  - `financial_deposit_accounts` — deposits
  - `financial_payment_accounts` — payments (see payment repo doc)
- Column groups:
  - Standard columns: `id`, `tx_date`, `receipt_number`, `received_from`, `payment_method`, `payment_reference`, plus all category columns defined in `CATEGORY_COLUMNS`.
- If you need to inspect or change the schema, check `database/migrations/` and `database/schema.sql` for migration examples.

Common maintenance tasks (junior dev checklist)
- Add a new category column:
  1. Add column in DB migration (`database/migrations/XXXX_add_new_category.sql`).
  2. Add the column name to `DepositAccountRepository::CATEGORY_COLUMNS` (preserve order).
  3. Add label in `DepositAccountRepository::CATEGORY_LABELS`.
  4. Update any CSS min-width in `financial.css` if table grows too wide.
  5. Test: Add a deposit and ensure sums and listing show the new column.
- Change validation (e.g., require `payment_reference` for bank): modify `FinancialController::validateDepositData()`.
- Change routes or access: update `features/shared/lib/routes.php` but be careful — router centrally enforces `requireAdmin()`.

Related files (quick links)
- Router: `features/shared/lib/routes.php`
- Pages: `features/financial/admin/pages/deposit-account.php`, `deposit-add.php`, `deposit-edit.php`
- Views: `features/financial/admin/views/deposit-account.php`, `deposit-add.php`
- Controller: `features/financial/admin/controllers/FinancialController.php`
- Repo: `features/financial/shared/lib/DepositAccountRepository.php`
- Delete handler: `features/financial/admin/ajax/deposit-delete.php`
- Styles: `features/financial/admin/assets/css/financial.css`
- Layouts: `features/shared/components/layouts/app-layout.php`, `features/shared/components/layouts/base.php`

If you want me to:
- Produce a migration example to add a new category column, or inspect `database/schema.sql` and list current table columns, tell me and I will prepare it.

---

**End of Deposit Account documentation**

Print Pages (Receipt)
- Files involved:
  - `features/financial/admin/pages/receipt-print.php` — the official receipt (Resit Rasmi) print template and logic.
  - Route: `/financial/receipt-print` registered in `features/shared/lib/routes.php` (router enforces `initSecureSession()`, `requireAuth()`, `requireAdmin()` before loading the page).
- What it does:
  - Reads `id` from `$_GET['id']` and validates it.
  - Loads the deposit row using `DepositAccountRepository::findById($id)`.
  - Computes `totalAmount` by summing all columns in `DepositAccountRepository::CATEGORY_COLUMNS` and selects a `categoryLabel` (first non-zero category) to use in the receipt description.
  - Formats date and converts numeric amount to words via `numberToWords()` (utility helper available in shared utilities).
  - Outputs a self-contained HTML document with embedded CSS optimized for printing (A5 style in this template) and a small `window.print()` script triggered on load.
  - Provides on-screen buttons (`.no-print`) for manual print/close; these are hidden when printing via `@media print` rules.
- Where to change content/style:
  - Header text and address are in the template HTML inside `receipt-print.php` — edit those strings to match your mosque details.
  - The layout (A5, lampiran label, signature boxes) is embedded in the file; adjust dimensions or classes there.
  - `amountInWords` uses `numberToWords()`; if you need another language or formatting, update the helper (search `numberToWords` in `features/shared/lib/utilities/functions.php`).
  - To disable auto-print, remove/comment the `window.print()` JS block at the end.
- Security and behaviour notes:
  - The route is admin-only. The page runs `initSecureSession()` and `requireAuth()` to verify sessions.
  - The template dies on invalid/missing `id` — consider replacing with user-friendly redirect or error page.

