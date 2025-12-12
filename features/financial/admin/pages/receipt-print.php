<?php
/**
 * Official Receipt Print Page (Resit Rasmi - Lampiran 6)
 * 
 * Generates a printable receipt document for deposit transactions.
 * Auto-prints when loaded.
 */

$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once $ROOT . '/features/financial/shared/lib/DepositAccountRepository.php';

initSecureSession();
requireAuth();

// Get deposit ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die('Invalid receipt ID.');
}

// Fetch deposit record
$repository = new DepositAccountRepository($mysqli);
$deposit = $repository->findById($id);

if (!$deposit) {
    die('Receipt not found.');
}

// Calculate total amount from category columns
$totalAmount = 0;
foreach (DepositAccountRepository::CATEGORY_COLUMNS as $col) {
    $totalAmount += (float)($deposit[$col] ?? 0);
}

// Determine category for display
$categoryLabel = '';
foreach (DepositAccountRepository::CATEGORY_COLUMNS as $col) {
    $val = (float)($deposit[$col] ?? 0);
    if ($val > 0) {
        $categoryLabel = DepositAccountRepository::CATEGORY_LABELS[$col] ?? $col;
        break; // Use the first non-zero category
    }
}

// Payment method display
$paymentMethodDisplay = 'Tunai';
if ($deposit['payment_method'] === 'cheque') {
    $paymentMethodDisplay = 'Bank (Cek)';
    if (!empty($deposit['payment_reference'])) {
        $paymentMethodDisplay .= ' - No. ' . htmlspecialchars($deposit['payment_reference']);
    }
} elseif ($deposit['payment_method'] === 'bank') {
    $paymentMethodDisplay = 'Bank (E-Banking)';
    if (!empty($deposit['payment_reference'])) {
        $paymentMethodDisplay .= ' - No. ' . htmlspecialchars($deposit['payment_reference']);
    }
}

// Format date
$formattedDate = date('d/m/Y', strtotime($deposit['tx_date']));

// Convert amount to words
$amountInWords = numberToWords($totalAmount);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resit Rasmi - <?php echo e($deposit['receipt_number'] ?? 'N/A'); ?></title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            background: #fff;
        }

        /* Print-specific styles */
        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .receipt-container {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: none !important;
                padding: 15mm !important; /* Restore padding for print */
                page-break-after: always;
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

            .receipt-container {
                width: 210mm; /* A4 width */
                min-height: 297mm; /* A4 height */
                margin: 0 auto;
                padding: 20mm;
                background: #fff;
                border: 1px solid #ccc;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
        }

        /* Layout Elements */
        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .receipt-title-box {
            background: #f2f2f2;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }

        .receipt-title-box h2 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .org-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .org-address {
            font-size: 10pt;
            margin-bottom: 15px;
        }

        /* Forms / Key-Value Pairs */
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            align-items: baseline;
            margin-bottom: 10px;
        }

        .col-left {
            flex: 0 0 60%;
            display: flex;
        }

        .col-right {
            flex: 0 0 40%;
            display: flex;
            padding-left: 20px;
        }

        .label {
            font-weight: bold;
            width: 110px; /* Fixed width for labels */
            flex-shrink: 0;
        }

        .value-line {
            flex-grow: 1;
            border-bottom: 1px dotted #999;
            padding-left: 5px;
            position: relative;
            top: -2px; /* Visual alignment with dots */
        }



        /* Payment Method Section */
        .payment-section {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 40px;
        }
        
        .payment-row {
            margin-top: 5px;
        }

        /* Bottom Section Container */
        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start; /* Align top */
            margin-top: 40px;
        }

        /* Summary/Payment Box (Left) */
        .summary-box {
            width: 45%;
            border: 1px solid #000;
        }

        .summary-row {
            display: flex;
            padding: 8px 10px;
        }
        
        .summary-row:first-child {
            border-bottom: 1px solid #000;
        }

        .summary-label {
            font-weight: bold;
            width: 120px;
        }

        .summary-value {
             flex: 1;
             text-align: right;
             font-weight: bold;
        }

        /* Signatures (Right) */
        .signature-section {
            width: 45%;
            /* Margins handled by parent flex container */
        }

        .value-box {
            border: 1px solid #000;
            padding: 5px 10px;
            min-width: 150px;
            display: inline-block;
            font-weight: bold;
            background: #fff;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Print buttons (hidden when printing) -->
    <div class="no-print">
        <button onclick="window.print()"><i class="fas fa-print"></i> Cetak Resit</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="receipt-container">
        <!-- Title Box -->
        <div class="receipt-title-box">
            <h2>RESIT RASMI</h2>
        </div>

        <!-- Org Header -->
        <div class="header-section">
            <div class="org-name">JAWATANKUASA MASJID DARUL ULUM</div>
            <div class="org-address">
                Taman Desa Ilmu,<br>
                94300 Kota Samarahan, Sarawak
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="row">
                <div class="col-left" style="align-items: center;">
                    <span class="label" style="width: auto; margin-right: 15px;">NO. RESIT</span>
                    <div class="value-box"><?php echo e($deposit['receipt_number'] ?? '-'); ?></div>
                </div>
                <div class="col-right">
                    <span class="label" style="width: 60px;">Tarikh:</span>
                    <span class="value-line"><?php echo e($formattedDate); ?></span>
                </div>
            </div>
            
            <div class="row">
                <span class="label">Diterima Dari:</span>
                <span class="value-line"><?php echo e($deposit['received_from'] ?? '-'); ?></span>
            </div>

            <div class="row">
                <span class="label">Jumlah:</span>
                <span class="value-line" style="font-style: italic; text-transform: uppercase;">
                    <?php echo e($amountInWords); ?> (RM <?php echo number_format($totalAmount, 2); ?>)
                </span>
            </div>

            <div class="row">
                <span class="label">Perkara:</span>
                <span class="value-line"><?php echo e($deposit['description'] ?? $categoryLabel); ?></span>
            </div>

        </div>

        <div class="bottom-section">
            <!-- Payment/Amount Box -->
            <div class="summary-box">
                <div class="summary-row">
                    <span class="summary-label">RM</span>
                    <span class="summary-value"><?php echo number_format($totalAmount, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">
                        <span style="<?php echo ($deposit['payment_method'] === 'cash') ? '' : 'text-decoration: line-through;'; ?>">TUNAI</span> / 
                        <span style="<?php echo ($deposit['payment_method'] !== 'cash') ? '' : 'text-decoration: line-through;'; ?>">BANK</span>
                    </span>
                    <span class="summary-value"><?php echo e($paymentMethodDisplay); ?></span>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="row">
                    <span class="label">Disediakan Oleh:</span>
                    <span class="value-line">&nbsp;</span>
                </div>
                <div class="row">
                    <span class="label">Tandatangan:</span>
                    <span class="value-line">&nbsp;</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            
        </div>
    </div>

    <!-- Auto-print script -->
    <script>
        // Auto-print when page loads (with slight delay for rendering)
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
