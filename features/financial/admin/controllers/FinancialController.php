<?php
/**
 * FinancialController - Handles all financial module admin operations
 */

// Include repositories
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/financial/shared/lib/PaymentAccountRepository.php';
require_once $ROOT . '/features/financial/shared/lib/DepositAccountRepository.php';
require_once $ROOT . '/features/financial/shared/lib/FinancialSettingsRepository.php';

class FinancialController {
    private mysqli $mysqli;
    private PaymentAccountRepository $paymentRepo;
    private DepositAccountRepository $depositRepo;
    private FinancialSettingsRepository $settingsRepo;

    public function __construct(mysqli $mysqli) {
        $this->mysqli = $mysqli;
        $this->paymentRepo = new PaymentAccountRepository($mysqli);
        $this->depositRepo = new DepositAccountRepository($mysqli);
        $this->settingsRepo = new FinancialSettingsRepository($mysqli);
    }

    public function index(): array {
        // Get current balance summary for dashboard
        $currentYear = (int)date('Y');
        $balances = $this->settingsRepo->calculateClosingBalances($currentYear);
        $settings = $this->settingsRepo->getCurrentSettings();
        
        return [
            'title' => 'Financial Management',
            'data' => [],
            'balances' => $balances,
            'settings' => $settings,
            'fiscalYear' => $currentYear,
        ];
    }

    // ==================== CASH BOOK METHODS ====================


    /**
     * Display the Cash Book (Buku Tunai)
     * @param int|null $fiscalYear Optional fiscal year filter (defaults to current year)
     */
    public function cashBook(?int $fiscalYear = null, ?int $month = null): array {
        $fiscalYear = $fiscalYear ?? (int)date('Y');
        // Default to current month if not specified, or allow '0' or null for "All" ? 
        // Let's assume if month is null, we show all (or user can select 'All'). 
        // But for better UX per user request "one month will have one cash book", maybe default to current month if not provided is safer, 
        // but let's stick to optional so 'All' is possible.
        
        // Get opening balances for the year from financial_settings
        $settings = $this->settingsRepo->getByFiscalYear($fiscalYear);
        $openingCash = (float)($settings['opening_cash_balance'] ?? 0);
        $openingBank = (float)($settings['opening_bank_balance'] ?? 0);
        
        $transactions = $this->getCashBookData($fiscalYear);
        
        // Start with year opening balances
        $tunaiBalance = $openingCash;
        $bankBalance = $openingBank;
        
        // Variables to hold the opening balance specific to the selected month
        $monthOpeningCash = $openingCash;
        $monthOpeningBank = $openingBank;
        
        $filteredTransactions = [];

        // Processing loop
        foreach ($transactions as &$tx) {
            $txDate = strtotime($tx['tx_date']);
            $txMonth = (int)date('m', $txDate);
            $amount = (float)$tx['amount'];
            
            // If we are filtering by month, and this transaction is BEFORE the requested month,
            // we apply it to the "month opening balance" but don't add it to the display list.
            $isBeforeSelectedMonth = ($month && $txMonth < $month);
            
            // Check if transaction is in the selected month (or if no month selected)
            $isInSelectedRange = (!$month || $txMonth === $month);

            if ($tx['type'] === 'IN') {
                if ($tx['payment_method'] === 'cash') {
                    $tunaiBalance += $amount;
                    if ($isBeforeSelectedMonth) $monthOpeningCash += $amount;
                } else {
                    $bankBalance += $amount;
                    if ($isBeforeSelectedMonth) $monthOpeningBank += $amount;
                }
            } else { // OUT
                if ($tx['payment_method'] === 'cash') {
                    $tunaiBalance -= $amount;
                    if ($isBeforeSelectedMonth) $monthOpeningCash -= $amount;
                } else {
                    $bankBalance -= $amount;
                    if ($isBeforeSelectedMonth) $monthOpeningBank -= $amount;
                }
            }
            
            $tx['tunai_balance'] = $tunaiBalance;
            $tx['bank_balance'] = $bankBalance;

            if ($isInSelectedRange) {
                $filteredTransactions[] = $tx;
            }
        }
        unset($tx);

        // If a month was selected, override the returned opening balances with the calculated ones
        if ($month) {
            $openingCash = $monthOpeningCash;
            $openingBank = $monthOpeningBank;
        }

        return [
            'title' => 'Buku Tunai',
            'transactions' => $filteredTransactions,
            'tunaiBalance' => $tunaiBalance, // Current closing balance (year-to-date or month-end depending on view?) - Usually existing cards show current state. Let's keep year-end or current total? 
            // Actually, if filtering by month, user might expect the balance cards to show the balance AT THE END OF THAT MONTH.
            // But the current logic ($tunaiBalance) holds the cumulative balance after iterating ALL transactions (if we iterated all to get running totals).
            // Wait, if I iterate all, $tunaiBalance is the FINAL balance of the year (or up to now).
            // If I view "January", I probably want to see the balance at end of January in the table.
            // The stat cards usually show "Current Balance" (Live).
            // Let's return the final calculated balance of the *filtered* set? 
            // No, the table footer should show the balance at the end of the view.
            
            // Let's refine: The loop goes through ALL transactions sorted by date.
            // If we stop displaying at month X, we still continue calculating to get the YTD total?
            // Yes, user might want to see YTD status in top cards, but month details in table.
            
            // However, the table footer uses $tunaiBalance. 
            // If I view January, the table footer should show Jan closing balance.
            // My loop above processes ALL transactions.
            // So $tunaiBalance at end of loop is DEC 31 (or latest).
            // If I want Month End balance, I should capture it.
            
            // Correction:
            // If I filter by Month, the "Running Balance" column in the table will show the correct running balance at that point in time.
            // The table footer typically shows the value of the last row.
            
            // Let's update the return to differentiate "Current System Balance" vs "View Closing Balance".
            
            'currentCashBalance' => $tunaiBalance, 
            'currentBankBalance' => $bankBalance,
            
            'openingCash' => $openingCash,
            'openingBank' => $openingBank,
            'fiscalYear' => $fiscalYear,
            'month' => $month,
            'hasSettings' => $settings !== null,
        ];
    }

    /**
     * Fetch combined transactions from deposits and payments
     * @param int|null $fiscalYear Optional fiscal year filter
     */
    private function getCashBookData(?int $fiscalYear = null): array {
        // Build dynamic sum clauses
        $depositSum = implode(' + ', array_map(fn($c) => "COALESCE($c, 0)", DepositAccountRepository::CATEGORY_COLUMNS));
        $paymentSum = implode(' + ', array_map(fn($c) => "COALESCE($c, 0)", PaymentAccountRepository::CATEGORY_COLUMNS));

        $yearFilter = $fiscalYear ? "WHERE YEAR(tx_date) = $fiscalYear" : "";

        $sql = "
            (SELECT 
                id, 
                tx_date, 
                receipt_number as ref_no, 
                description, 
                ($depositSum) as amount, 
                payment_method, 
                'IN' as type 
            FROM financial_deposit_accounts $yearFilter)
            
            UNION ALL
            
            (SELECT 
                id, 
                tx_date, 
                voucher_number as ref_no, 
                description, 
                ($paymentSum) as amount, 
                payment_method, 
                'OUT' as type 
            FROM financial_payment_accounts $yearFilter)
            
            ORDER BY tx_date ASC, id ASC
        ";

        $result = $this->mysqli->query($sql);
        
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        return $rows;
    }

    // ==================== PAYMENT ACCOUNT METHODS ====================

    /**
     * List all payment records
     */
    public function paymentAccount(): array {
        $payments = $this->paymentRepo->findAll();
        
        $totalCash = 0;
        $totalBank = 0;
        foreach ($payments as $row) {
            $amount = $this->paymentRepo->calculateRowTotal($row);
            if (($row['payment_method'] ?? 'cash') === 'cash') {
                $totalCash += $amount;
            } else {
                $totalBank += $amount;
            }
        }

        return [
            'title' => 'Akaun Bayaran',
            'payments' => $payments,
            'categoryColumns' => PaymentAccountRepository::CATEGORY_COLUMNS,
            'categoryLabels' => PaymentAccountRepository::CATEGORY_LABELS,
            'totalCash' => $totalCash,
            'totalBank' => $totalBank,
        ];
    }

    /**
     * Show add payment form
     */
    public function addPayment(): array {
        // Generate the next voucher number for preview
        $nextVoucherNumber = $this->paymentRepo->generateVoucherNumber();
        
        return [
            'title' => 'Add Payment',
            'record' => null,
            'categoryColumns' => PaymentAccountRepository::CATEGORY_COLUMNS,
            'categoryLabels' => PaymentAccountRepository::CATEGORY_LABELS,
            'nextVoucherNumber' => $nextVoucherNumber,
            'errors' => [],
            'old' => [],
        ];
    }

    /**
     * Store a new payment record
     */
    public function storePayment(array $postData): array {
        $errors = $this->validatePaymentData($postData);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'old' => $postData,
            ];
        }

        $this->paymentRepo->create($postData);
        return ['success' => true];
    }

    /**
     * Show edit payment form
     */
    public function editPayment(int $id): array {
        $record = $this->paymentRepo->findById($id);
        if (!$record) {
            return [
                'title' => 'Edit Payment',
                'record' => null,
                'errors' => ['Record not found.'],
                'categoryColumns' => PaymentAccountRepository::CATEGORY_COLUMNS,
                'categoryLabels' => PaymentAccountRepository::CATEGORY_LABELS,
                'old' => [],
            ];
        }

        return [
            'title' => 'Edit Payment',
            'record' => $record,
            'categoryColumns' => PaymentAccountRepository::CATEGORY_COLUMNS,
            'categoryLabels' => PaymentAccountRepository::CATEGORY_LABELS,
            'errors' => [],
            'old' => [],
        ];
    }

    /**
     * Update an existing payment record
     */
    public function updatePayment(int $id, array $postData): array {
        $errors = $this->validatePaymentData($postData);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'old' => $postData,
            ];
        }

        $this->paymentRepo->update($id, $postData);
        return ['success' => true];
    }

    /**
     * Delete a payment record
     */
    public function deletePayment(int $id): array {
        $record = $this->paymentRepo->findById($id);
        if (!$record) {
            return ['success' => false, 'error' => 'Record not found.'];
        }

        $this->paymentRepo->delete($id);
        return ['success' => true];
    }

    /**
     * Validate payment form data
     */
    /**
     * Validate payment form data
     */
    private function validatePaymentData(array $data): array {
        $errors = [];

        if (empty($data['tx_date'])) {
            $errors[] = 'Date (Tarikh) is required.';
        }

        if (empty($data['description'])) {
            $errors[] = 'Description (Butiran) is required.';
        }

        // New validations
        if (empty($data['paid_to'])) {
            $errors[] = 'Paid To (Dibayar Kepada) is required.';
        }

        if (empty($data['payment_method'])) {
            $errors[] = 'Payment Method (Kaedah Pembayaran) is required.';
        }

        // Check that at least one category has a positive amount
        $hasAmount = false;
        foreach (PaymentAccountRepository::CATEGORY_COLUMNS as $col) {
            if (!empty($data[$col]) && is_numeric($data[$col]) && $data[$col] > 0) {
                $hasAmount = true;
                break;
            }
        }

        if (!$hasAmount) {
            $errors[] = 'At least one category must have an amount greater than 0.';
        }

        return $errors;
    }

    // ==================== DEPOSIT ACCOUNT METHODS ====================

    /**
     * List all deposit records
     */
    public function depositAccount(): array {
        $deposits = $this->depositRepo->findAll();
        
        $totalCash = 0;
        $totalBank = 0;
        foreach ($deposits as $row) {
            $amount = $this->depositRepo->calculateRowTotal($row);
            if (($row['payment_method'] ?? 'cash') === 'cash') {
                $totalCash += $amount;
            } else {
                $totalBank += $amount;
            }
        }

        return [
            'title' => 'Akaun Terimaan',
            'deposits' => $deposits,
            'categoryColumns' => DepositAccountRepository::CATEGORY_COLUMNS,
            'categoryLabels' => DepositAccountRepository::CATEGORY_LABELS,
            'totalCash' => $totalCash,
            'totalBank' => $totalBank,
        ];
    }

    /**
     * Show add deposit form
     */
    public function addDeposit(): array {
        return [
            'title' => 'Add Deposit',
            'record' => null,
            'categoryColumns' => DepositAccountRepository::CATEGORY_COLUMNS,
            'categoryLabels' => DepositAccountRepository::CATEGORY_LABELS,
            'errors' => [],
            'old' => [],
        ];
    }

    /**
     * Store a new deposit record
     */
    public function storeDeposit(array $postData): array {
        $errors = $this->validateDepositData($postData);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'old' => $postData,
            ];
        }

        $this->depositRepo->create($postData);
        return ['success' => true];
    }

    /**
     * Show edit deposit form
     */
    public function editDeposit(int $id): array {
        $record = $this->depositRepo->findById($id);
        if (!$record) {
            return [
                'title' => 'Edit Deposit',
                'record' => null,
                'errors' => ['Record not found.'],
                'categoryColumns' => DepositAccountRepository::CATEGORY_COLUMNS,
                'categoryLabels' => DepositAccountRepository::CATEGORY_LABELS,
                'old' => [],
            ];
        }

        return [
            'title' => 'Edit Deposit',
            'record' => $record,
            'categoryColumns' => DepositAccountRepository::CATEGORY_COLUMNS,
            'categoryLabels' => DepositAccountRepository::CATEGORY_LABELS,
            'errors' => [],
            'old' => [],
        ];
    }

    /**
     * Update an existing deposit record
     */
    public function updateDeposit(int $id, array $postData): array {
        $errors = $this->validateDepositData($postData);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'old' => $postData,
            ];
        }

        $this->depositRepo->update($id, $postData);
        return ['success' => true];
    }

    /**
     * Delete a deposit record
     */
    public function deleteDeposit(int $id): array {
        $record = $this->depositRepo->findById($id);
        if (!$record) {
            return ['success' => false, 'error' => 'Record not found.'];
        }

        $this->depositRepo->delete($id);
        return ['success' => true];
    }

    /**
     * Validate deposit form data
     */
    private function validateDepositData(array $data): array {
        $errors = [];

        if (empty($data['tx_date'])) {
            $errors[] = 'Date (Tarikh) is required.';
        }

        if (empty($data['description'])) {
            $errors[] = 'Description (Butiran) is required.';
        }

        // New validations
        if (empty($data['received_from'])) {
            $errors[] = 'Received From (Diterima Dari) is required.';
        }

        if (empty($data['payment_method'])) {
            $errors[] = 'Payment Method (Kaedah Pembayaran) is required.';
        }

        // Check that at least one category has a positive amount
        $hasAmount = false;
        foreach (DepositAccountRepository::CATEGORY_COLUMNS as $col) {
            if (!empty($data[$col]) && is_numeric($data[$col]) && $data[$col] > 0) {
                $hasAmount = true;
                break;
            }
        }

        if (!$hasAmount) {
            $errors[] = 'At least one category must have an amount greater than 0.';
        }

        return $errors;
    }

    /**
     * Get the payment repository (for external access if needed)
     */
    public function getPaymentRepository(): PaymentAccountRepository {
        return $this->paymentRepo;
    }

    /**
     * Get the deposit repository (for external access if needed)
     */
    public function getDepositRepository(): DepositAccountRepository {
        return $this->depositRepo;
    }

    // ==================== FINANCIAL STATEMENT METHODS ====================

    /**
     * Generate Financial Statement (Penyata Terimaan dan Bayaran)
     * 
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array
     */
    public function financialStatement(string $startDate, string $endDate): array {
        // A. Calculate Opening Balance (Baki Awal) - transactions before start_date
        $openingBalance = $this->calculateOpeningBalance($startDate);

        // B. Calculate Current Period Totals
        $currentDeposits = $this->getDepositTotals($startDate, $endDate);
        $currentPayments = $this->getPaymentTotals($startDate, $endDate);

        // C. Calculate Closing Balance (Baki Akhir)
        $closingCash = $openingBalance['cash'] + $currentDeposits['by_method']['cash'] - $currentPayments['by_method']['cash'];
        $closingBank = $openingBalance['bank'] + $currentDeposits['by_method']['bank'] - $currentPayments['by_method']['bank'];

        // Calculate surplus/deficit
        $totalTerimaan = $currentDeposits['total'];
        $totalBayaran = $currentPayments['total'];
        $surplusDeficit = $totalTerimaan - $totalBayaran;

        return [
            'title' => 'Penyata Terimaan dan Bayaran',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'openingBalance' => $openingBalance,
            'deposits' => $currentDeposits,
            'payments' => $currentPayments,
            'totalTerimaan' => $totalTerimaan,
            'totalBayaran' => $totalBayaran,
            'surplusDeficit' => $surplusDeficit,
            'closingBalance' => [
                'cash' => $closingCash,
                'bank' => $closingBank,
                'total' => $closingCash + $closingBank,
            ],
            'depositCategoryLabels' => DepositAccountRepository::CATEGORY_LABELS,
            'paymentCategoryLabels' => PaymentAccountRepository::CATEGORY_LABELS,
        ];
    }

    /**
     * Calculate opening balance (transactions before start date)
     */
    private function calculateOpeningBalance(string $startDate): array {
        // Sum deposits before start_date by payment method
        $depositSumClause = implode(' + ', array_map(fn($c) => "COALESCE($c, 0)", DepositAccountRepository::CATEGORY_COLUMNS));
        
        $sqlDeposits = "
            SELECT 
                payment_method,
                SUM($depositSumClause) as total
            FROM financial_deposit_accounts
            WHERE tx_date < ?
            GROUP BY payment_method
        ";

        $stmt = $this->mysqli->prepare($sqlDeposits);
        $stmt->bind_param('s', $startDate);
        $stmt->execute();
        $result = $stmt->get_result();

        $depositCash = 0.0;
        $depositBank = 0.0;
        while ($row = $result->fetch_assoc()) {
            if ($row['payment_method'] === 'cash') {
                $depositCash = (float)$row['total'];
            } else {
                $depositBank += (float)$row['total'];
            }
        }
        $stmt->close();

        // Sum payments before start_date by payment method
        $paymentSumClause = implode(' + ', array_map(fn($c) => "COALESCE($c, 0)", PaymentAccountRepository::CATEGORY_COLUMNS));
        
        $sqlPayments = "
            SELECT 
                payment_method,
                SUM($paymentSumClause) as total
            FROM financial_payment_accounts
            WHERE tx_date < ?
            GROUP BY payment_method
        ";

        $stmt = $this->mysqli->prepare($sqlPayments);
        $stmt->bind_param('s', $startDate);
        $stmt->execute();
        $result = $stmt->get_result();

        $paymentCash = 0.0;
        $paymentBank = 0.0;
        while ($row = $result->fetch_assoc()) {
            if ($row['payment_method'] === 'cash') {
                $paymentCash = (float)$row['total'];
            } else {
                $paymentBank += (float)$row['total'];
            }
        }
        $stmt->close();

        return [
            'cash' => $depositCash - $paymentCash,
            'bank' => $depositBank - $paymentBank,
            'total' => ($depositCash - $paymentCash) + ($depositBank - $paymentBank),
        ];
    }

    /**
     * Get deposit totals for the period, grouped by category and payment method
     */
    private function getDepositTotals(string $startDate, string $endDate): array {
        $categoryColumns = DepositAccountRepository::CATEGORY_COLUMNS;
        
        // Build SELECT clause for category sums
        $selectClauses = [];
        foreach ($categoryColumns as $col) {
            $selectClauses[] = "SUM(COALESCE($col, 0)) as $col";
        }
        $selectClause = implode(', ', $selectClauses);

        // Get totals by category
        $sql = "SELECT $selectClause FROM financial_deposit_accounts WHERE tx_date BETWEEN ? AND ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $categoryTotals = $result->fetch_assoc();
        $stmt->close();

        // Calculate grand total
        $grandTotal = 0.0;
        foreach ($categoryColumns as $col) {
            $grandTotal += (float)($categoryTotals[$col] ?? 0);
        }

        // Get totals by payment method
        $depositSumClause = implode(' + ', array_map(fn($c) => "COALESCE($c, 0)", $categoryColumns));
        $sqlMethod = "
            SELECT 
                payment_method,
                SUM($depositSumClause) as total
            FROM financial_deposit_accounts
            WHERE tx_date BETWEEN ? AND ?
            GROUP BY payment_method
        ";
        $stmt = $this->mysqli->prepare($sqlMethod);
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        $byMethod = ['cash' => 0.0, 'bank' => 0.0];
        while ($row = $result->fetch_assoc()) {
            if ($row['payment_method'] === 'cash') {
                $byMethod['cash'] = (float)$row['total'];
            } else {
                $byMethod['bank'] += (float)$row['total'];
            }
        }
        $stmt->close();

        return [
            'by_category' => $categoryTotals,
            'by_method' => $byMethod,
            'total' => $grandTotal,
        ];
    }

    /**
     * Get payment totals for the period, grouped by category and payment method
     */
    private function getPaymentTotals(string $startDate, string $endDate): array {
        $categoryColumns = PaymentAccountRepository::CATEGORY_COLUMNS;
        
        // Build SELECT clause for category sums
        $selectClauses = [];
        foreach ($categoryColumns as $col) {
            $selectClauses[] = "SUM(COALESCE($col, 0)) as $col";
        }
        $selectClause = implode(', ', $selectClauses);

        // Get totals by category
        $sql = "SELECT $selectClause FROM financial_payment_accounts WHERE tx_date BETWEEN ? AND ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $categoryTotals = $result->fetch_assoc();
        $stmt->close();

        // Calculate grand total
        $grandTotal = 0.0;
        foreach ($categoryColumns as $col) {
            $grandTotal += (float)($categoryTotals[$col] ?? 0);
        }

        // Get totals by payment method
        $paymentSumClause = implode(' + ', array_map(fn($c) => "COALESCE($c, 0)", $categoryColumns));
        $sqlMethod = "
            SELECT 
                payment_method,
                SUM($paymentSumClause) as total
            FROM financial_payment_accounts
            WHERE tx_date BETWEEN ? AND ?
            GROUP BY payment_method
        ";
        $stmt = $this->mysqli->prepare($sqlMethod);
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        $byMethod = ['cash' => 0.0, 'bank' => 0.0];
        while ($row = $result->fetch_assoc()) {
            if ($row['payment_method'] === 'cash') {
                $byMethod['cash'] = (float)$row['total'];
            } else {
                $byMethod['bank'] += (float)$row['total'];
            }
        }
        $stmt->close();

        return [
            'by_category' => $categoryTotals,
            'by_method' => $byMethod,
            'total' => $grandTotal,
        ];
    }

    // ==================== FINANCIAL SETTINGS METHODS ====================

    /**
     * Get financial settings form data
     */
    public function financialSettings(): array {
        $currentYear = (int)date('Y');
        $settings = $this->settingsRepo->getCurrentSettings();
        $allSettings = $this->settingsRepo->findAll();
        
        // Generate available fiscal years (current year ± 5 years)
        $availableYears = [];
        for ($y = $currentYear - 5; $y <= $currentYear + 1; $y++) {
            $availableYears[] = $y;
        }
        
        return [
            'title' => 'Financial Settings',
            'settings' => $settings,
            'allSettings' => $allSettings,
            'availableYears' => $availableYears,
            'currentYear' => $currentYear,
            'errors' => [],
            'success' => false,
        ];
    }

    /**
     * Save financial settings (opening balances)
     */
    public function saveFinancialSettings(array $postData): array {
        $errors = [];
        
        // Validate
        if (empty($postData['fiscal_year'])) {
            $errors[] = 'Fiscal year is required.';
        }
        
        if (!isset($postData['opening_cash_balance']) || !is_numeric($postData['opening_cash_balance'])) {
            $errors[] = 'Opening cash balance must be a valid number.';
        }
        
        if (!isset($postData['opening_bank_balance']) || !is_numeric($postData['opening_bank_balance'])) {
            $errors[] = 'Opening bank balance must be a valid number.';
        }
        
        if (empty($postData['effective_date'])) {
            $errors[] = 'Effective date is required.';
        }
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'old' => $postData,
            ];
        }
        
        // Add created_by if user is logged in
        if (isset($_SESSION['user_id'])) {
            $postData['created_by'] = $_SESSION['user_id'];
        }
        
        $success = $this->settingsRepo->save($postData);
        
        return [
            'success' => $success,
            'errors' => $success ? [] : ['Failed to save settings.'],
        ];
    }

    /**
     * Get balance summary for a fiscal year
     */
    public function getBalanceSummary(?int $fiscalYear = null): array {
        $fiscalYear = $fiscalYear ?? (int)date('Y');
        return $this->settingsRepo->calculateClosingBalances($fiscalYear);
    }
}
