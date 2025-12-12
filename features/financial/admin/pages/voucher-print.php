<?php
/**
 * Payment Voucher Print Page (Baucar Bayaran - Lampiran 1)
 * 
 * Generates a printable voucher document for payment transactions.
 * Auto-prints when loaded.
 */

$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once $ROOT . '/features/financial/shared/lib/PaymentAccountRepository.php';

initSecureSession();
requireAuth();

// Get payment ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die('Invalid voucher ID.');
}

// Fetch payment record
$repository = new PaymentAccountRepository($mysqli);
$payment = $repository->findById($id);

if (!$payment) {
    die('Voucher not found.');
}

// Calculate total amount from category columns
$totalAmount = 0;
$categories = [];
foreach (PaymentAccountRepository::CATEGORY_COLUMNS as $col) {
    $val = (float)($payment[$col] ?? 0);
    if ($val > 0) {
        $totalAmount += $val;
        $categories[] = [
            'label' => PaymentAccountRepository::CATEGORY_LABELS[$col] ?? $col,
            'amount' => $val
        ];
    }
}

// Payment method display
$isCash = $payment['payment_method'] === 'cash';
$isBank = $payment['payment_method'] !== 'cash';

// Format date
$formattedDate = date('d/m/Y', strtotime($payment['tx_date']));

// Convert amount to words
$amountInWords = numberToWords($totalAmount);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baucar Bayaran - <?php echo e($payment['voucher_number'] ?? 'N/A'); ?></title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            background: #fff;
        }

        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 20mm; /* Simulate page margin here */
                -webkit-print-color-adjust: exact;
            }
            
            @page {
                size: A4;
                margin: 0; /* Hides browser default headers/footers */
            }
            
            .no-print {
                display: none !important;
            }
            
            .voucher-container {
                border: 2px solid #000 !important;
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                height: auto !important;
            }
        }

        /* Screen preview styles */
        @media screen {
            body {
                background: #f0f0f0;
                padding: 20px;
            }
            
            .no-print {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .no-print button {
                padding: 10px 30px;
                font-size: 14pt;
                cursor: pointer;
                background: #4a90d9;
                color: #fff;
                border: none;
                border-radius: 4px;
                margin: 0 10px;
            }
            
            .no-print button:hover {
                background: #357abd;
            }
            
            .voucher-container {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #fff;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
        }

        /* Main Container */
        .voucher-container {
            border: 2px solid #000;
            padding: 15px;
            position: relative;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .org-name {
            font-size: 11pt;
            font-style: italic;
            display: inline-block;
            min-width: 80%;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .org-name-label {
            font-size: 8pt;
            font-style: italic;
            margin-bottom: 5px;
        }

        .org-address {
            font-size: 10pt;
            font-style: italic;
            display: inline-block;
            min-width: 80%;
            margin-bottom: 5px;
        }

        .org-address-label {
            font-size: 8pt;
            font-style: italic;
        }

        /* Info Grid */
        .info-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .col-left {
            flex: 1.2;
        }

        .col-right {
            flex: 1;
        }

        .field-row {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .field-label {
            width: 140px;
            font-size: 9pt;
        }

        .field-input {
            flex: 1;
            border: 1px solid #000;
            height: 24px;
            padding: 2px 5px;
            font-size: 10pt;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            border: 1px solid #000;
            padding: 2px;
            font-size: 9pt;
            flex: 1;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            margin: 1px 0;
        }

        .checkbox-box {
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            margin-left: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        /* Table */
        .voucher-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 0;
        }

        .voucher-table th, .voucher-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .voucher-table th {
            background-color: #ccc;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }

        .col-no { width: 40px; text-align: center; }
        .col-desc { text-align: left; }
        .col-amount { width: 120px; text-align: right; }

        .total-row td {
            font-weight: bold;
        }

        /* Amount Words */
        .amount-words-row {
            border: 1px solid #000;
            border-top: none;
            padding: 5px;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        /* Authorization Grid */
        .auth-grid {
            border: 1px solid #000;
            display: flex;
        }

        .auth-col-left {
            width: 50%;
            border-right: 1px solid #000;
            display: flex;
            flex-direction: column;
        }

        .auth-col-right {
            width: 50%;
            display: flex;
            flex-direction: column;
        }

        .auth-box {
            padding: 5px;
            min-height: 120px;
            position: relative;
        }

        .auth-box-border-bottom {
            border-bottom: 1px solid #000;
        }

        .auth-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 40px;
        }

        .auth-fields {
            margin-top: auto;
            font-size: 9pt;
        }

        .auth-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 100%;
            margin-bottom: 2px;
        }
        
        .auth-row {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="voucher-container">
        <!-- Header -->
        <div class="header">
            <div class="header-title">BAUCAR BAYARAN</div>
            
            <div class="org-name">JAWATANKUASA PENGURUSAN MASJID DARUL ULUM</div>
            
            <div class="org-address">TAMAN DESA ILMU, 94300 KOTA SAMARAHAN, SARAWAK</div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Left Column -->
            <div class="col-left">
                <div class="field-row">
                    <div class="field-label">BAYAR KEPADA</div>
                    <div class="field-input"><?php echo e($payment['paid_to'] ?? ''); ?></div>
                </div>
                <div class="field-row">
                    <div class="field-label">NO. KAD PENGENALAN</div>
                    <div class="field-input"><?php echo e($payment['payee_ic'] ?? ''); ?></div>
                </div>
                <div class="field-row">
                    <div class="field-label">NAMA BANK</div>
                    <div class="field-input"><?php echo e($payment['payee_bank_name'] ?? ''); ?></div>
                </div>
                <div class="field-row">
                    <div class="field-label">NO. AKAUN</div>
                    <div class="field-input"><?php echo e($payment['payee_bank_account'] ?? ''); ?></div>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="col-right">
                <div class="field-row">
                    <div class="field-label" style="width: 100px;">NO. BAUCAR</div>
                    <div class="field-input"><?php echo e($payment['voucher_number'] ?? ''); ?></div>
                </div>
                <div class="field-row">
                    <div class="field-label" style="width: 100px;">TARIKH</div>
                    <div class="field-input"><?php echo $formattedDate; ?></div>
                </div>
                <div class="field-row">
                    <div class="field-label" style="width: 100px;">NO. RUJUKAN</div>
                    <div class="field-input"><?php echo e($payment['payment_reference'] ?? ''); ?></div>
                </div>
                <div class="field-row" style="align-items: flex-start;">
                    <div class="field-label" style="width: 100px; margin-top: 5px;">KAEDAH PEMBAYARAN</div>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <div class="checkbox-box"><?php echo ($payment['payment_method'] === 'cash') ? '✓' : ''; ?></div>
                            TUNAI
                        </div>
                        <div class="checkbox-item">
                            <div class="checkbox-box"><?php echo ($payment['payment_method'] === 'cheque') ? '✓' : ''; ?></div>
                            CEK
                        </div>
                        <div class="checkbox-item">
                            <div class="checkbox-box"><?php echo ($payment['payment_method'] === 'bank') ? '✓' : ''; ?></div>
                            E-BANKING
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Table -->
        <table class="voucher-table">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th class="col-desc">BUTIRAN BAYARAN</th>
                    <th class="col-amount">AMAUN (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categories)): ?>
                    <?php $itemNo = 1; ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="col-no"><?php echo $itemNo++; ?></td>
                        <td><?php echo e($payment['description'] ?? $cat['label']); ?></td>
                        <td class="col-amount"><?php echo number_format($cat['amount'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="col-no">1</td>
                        <td><?php echo e($payment['description'] ?? '-'); ?></td>
                        <td class="col-amount"><?php echo number_format($totalAmount, 2); ?></td>
                    </tr>
                <?php endif; ?>
                
                <!-- Fillers to main consistency -->
                <?php for($i=0; $i<3; $i++): ?>
                <tr>
                    <td class="col-no">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="col-amount">&nbsp;</td>
                </tr>
                <?php endfor; ?>
                
                <tr class="total-row">
                    <td colspan="2" style="text-align: right; border-right: 1px solid #000;">JUMLAH (RM)</td>
                    <td class="col-amount"><?php echo number_format($totalAmount, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Amount Words -->
        <div class="amount-words-row">
            <strong>AMAUN (DALAM PERKATAAN) RINGGIT MALAYSIA :</strong> <?php echo e($amountInWords); ?>
        </div>

        <!-- Signatures (Auth Grid) -->
        <div class="auth-grid">
            <!-- Left Column: Prepared + Recipient -->
            <div class="auth-col-left">
                <!-- Box 1: Prepared By -->
                <div class="auth-box auth-box-border-bottom" style="flex: 1; display:flex; flex-direction:column;">
                    <div class="auth-title">DISEDIAKAN OLEH</div>
                    <div style="margin-top: auto;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 4px;"></div>
                        <div class="auth-fields">
                             <div class="auth-row">NAMA:</div>
                             <div class="auth-row">JAWATAN:</div>
                             <div class="auth-row">TARIKH:</div>
                        </div>
                    </div>
                </div>
                <!-- Box 2: Recipient -->
                <div class="auth-box" style="flex: 1; display:flex; flex-direction:column;">
                     <div class="auth-row" style="font-style: italic; margin-bottom: 10px;">
                        SAYA MENGESAHKAN PEMBAYARAN SEPERTI DI ATAS TELAH DITERIMA
                    </div>
                    <div style="margin-top: auto;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 4px;"></div>
                        <div class="auth-fields">
                             <div class="auth-row">NAMA:</div>
                             <div class="auth-row">NO. KAD PENGENALAN:</div>
                             <div class="auth-row">TARIKH:</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Approvers -->
            <div class="auth-col-right">
                <!-- Approver 1 -->
                <div class="auth-box auth-box-border-bottom" style="flex: 1; display:flex; flex-direction:column;">
                    <div class="auth-title">DISEMAK DAN DILULUSKAN OLEH</div>
                    <div style="margin-top: auto;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 4px;"></div>
                        <div class="auth-fields">
                            <div class="auth-row">NAMA:</div>
                            <div class="auth-row">JAWATAN:</div>
                            <div class="auth-row">TARIKH:</div>
                        </div>
                    </div>
                </div>
                <!-- Approver 2 -->
                <div class="auth-box auth-box-border-bottom" style="flex: 1; display:flex; flex-direction:column;">
                    <div class="auth-title">DISEMAK DAN DILULUSKAN OLEH</div>
                    <div style="margin-top: auto;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 4px;"></div>
                        <div class="auth-fields">
                            <div class="auth-row">NAMA:</div>
                            <div class="auth-row">JAWATAN:</div>
                            <div class="auth-row">TARIKH:</div>
                        </div>
                    </div>
                </div>
                <!-- Approver 3 -->
                <div class="auth-box" style="flex: 1; display:flex; flex-direction:column;">
                    <div class="auth-title">DISEMAK DAN DILULUSKAN OLEH</div>
                    <div style="margin-top: auto;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 4px;"></div>
                        <div class="auth-fields">
                            <div class="auth-row">NAMA:</div>
                            <div class="auth-row">JAWATAN:</div>
                            <div class="auth-row">TARIKH:</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            // setTimeout(() => window.print(), 500); 
        };
    </script>
</body>
</html>
