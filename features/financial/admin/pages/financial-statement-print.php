<?php
/**
 * Financial Statement Print Page (Penyata Terimaan dan Bayaran - Lampiran 9)
 */

$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once $ROOT . '/features/financial/shared/lib/FinancialStatementController.php';

initSecureSession();
requireAuth();

// Get date range from URL
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');

// Fetch data
$controller = new FinancialStatementController($mysqli);
$data = $controller->getStatementData($startDate, $endDate);

// Format dates for display
$displayStartDate = date('d/m/Y', strtotime($startDate));
$displayEndDate = date('d/m/Y', strtotime($endDate));
$periodString = "{$displayStartDate} HINGGA {$displayEndDate}";

?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyata Terimaan dan Bayaran</title>
    <style>
        /* Reset and base styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 10pt; line-height: 1.3; color: #000; background: #fff; }

        /* Print styles */
        @media print {
            @page { margin: 0; }
            body { margin: 0; padding: 15mm !important; } /* Restore visual margin effectively */
            .no-print { display: none !important; }
            .page-container { border: none !important; box-shadow: none !important; margin: 0 !important; width: 100% !important; }
        }

        /* Screen styles */
        @media screen {
            body { background: #f0f0f0; padding: 20px; }
            .no-print { text-align: center; margin-bottom: 20px; }
            .no-print button { padding: 10px 20px; font-size: 12pt; cursor: pointer; background: #4a90d9; color: #fff; border: none; border-radius: 4px; margin: 0 5px; }
            .page-container { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        }

        /* Layout */
        .page-container { position: relative; }
        .lampiran-label { position: absolute; top: 0; right: 0; text-align: right; font-weight: bold; }
        
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; }
        .header-line { border-bottom: 1px solid #000; margin: 5px 0; padding-bottom: 2px; }
        .header-text { font-style: italic; font-size: 10pt; }

        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; text-transform: uppercase; }
        
        .statement-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .statement-table td { padding: 4px; vertical-align: top; }
        .statement-table tr { break-inside: avoid; page-break-inside: avoid; } /* Prevent rows from splitting */
        
        /* Flexible layout relying on the single table structure for alignment */
        .col-label { width: 1%; white-space: nowrap; padding-right: 15px; vertical-align: top; }
        .col-box { width: auto; vertical-align: top; }
        .col-nota { width: 15%; text-align: center; vertical-align: top; }
        .col-total { width: 1%; white-space: nowrap; text-align: right; vertical-align: top; }

        .box-container { border: 1px solid #000; padding: 8px; font-size: 10pt; }
        .box-item { display: flex; justify-content: space-between; margin-bottom: 2px; gap: 15px; align-items: flex-start; }
        .box-item span:first-child { flex: 1; word-wrap: break-word; overflow-wrap: break-word; }
        .box-item span:last-child { white-space: nowrap; }
        
        .row-item { margin-bottom: 2px; height: auto; min-height: 1.3em; }
        
        .amount-underline { border-bottom: 1px solid #000; display: inline-block; min-width: 80px; text-align: right; padding-bottom: 1px; }
        .amount-underline-bold { border-bottom: 4px solid #000; display: inline-block; min-width: 80px; text-align: right; padding-bottom: 1px; }
        .amount-double { border-bottom: 3px double #000; display: inline-block; min-width: 80px; text-align: right; padding-bottom: 1px; }
        .amount-double-custom { 
            border-bottom: 1px solid #000; 
            display: inline-block; 
            min-width: 80px; 
            text-align: right; 
            padding-bottom: 1px; 
            position: relative; 
            margin-bottom: 3px; 
        }
        .amount-double-custom::after {
            content: '';
            position: absolute;
            left: 0;
            width: 100%;
            bottom: -4px;
            border-bottom: 2px solid #000;
        }

        .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
        .sig-box { width: 30%; border: 1px solid #000; padding: 10px; height: 120px; font-size: 9pt; }
        .sig-title { margin-bottom: 40px; }

        .text-right { text-align: right; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="page-container">

        <div class="header">
            <h1>PENYATA TERIMAAN DAN BAYARAN</h1>
            
            <div class="header-line">
                (JAWATANKUASA PENGURUSAN MASJID DARUL ULUM)
            </div>
            
            <div class="header-line">
                (LORONG DESA ILMU 22, 94300 KOTA SAMARAHAN, SARAWAK)
            </div>

            <div style="margin-top: 20px;">
                BAGI <span style="border-bottom: 1px solid #000; padding: 0 10px;"><?php echo $periodString; ?></span>
                <br>
                <span class="header-text">(tempoh / tahun berakhir)</span>
            </div>
        </div>

        <!-- Single Table for Alignment -->
        <table class="statement-table">
            <!-- Headers -->
            <thead>
                <tr>
                    <td colspan="2"></td>
                    <td style="text-align: center; font-weight: bold;">Nota</td>
                    <td style="text-align: right; font-weight: bold; vertical-align: bottom;">
                        <div style="display: inline-block; text-align: center; min-width: 60px;">
                            <?php echo date('Y', strtotime($endDate)); ?>
                            <div style="border-bottom: 1px solid #000;"></div>
                            (tahun)<br>RM
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <!-- Opening Balance -->
                <tr>
                    <td colspan="4" class="bold" style="padding-top: 15px;">
                        BAKI PADA <?php echo $displayStartDate; ?>
                    </td>
                </tr>
                <tr>
                    <td class="col-label">
                        <div style="padding-top: 10px;">
                            <div class="row-item">Wang Tunai di tangan</div>
                            <div class="row-item">Wang Tunai di bank</div>
                            <div class="row-item">Pelaburan</div>
                        </div>
                    </td>
                    <td class="col-box">
                        <div class="box-container">
                            <div class="row-item text-right"><?php echo number_format($data['opening_balance']['cash'], 2); ?></div>
                            <div class="row-item text-right"><?php echo number_format($data['opening_balance']['bank'], 2); ?></div>
                            <div class="row-item text-right">0.00</div>
                        </div>
                    </td>
                    <td class="col-nota"></td>
                    <td class="col-total">
                        <div style="padding-top: 10px;">
                            <div class="row-item"></div>
                            <div class="row-item"></div>
                            <div class="row-item">
                                <span class="amount-underline">
                                    <?php echo number_format($data['opening_balance']['cash'] + $data['opening_balance']['bank'], 2); ?>
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Receipts -->
                <tr>
                    <td colspan="4" class="bold" style="padding-top: 15px;">
                        A. TERIMAAN
                    </td>
                </tr>
                <tr>
                    <td class="col-label"></td>
                    <td class="col-box">
                        <div class="box-container">
                            <?php foreach ($data['receipts'] as $item): ?>
                            <div class="box-item">
                                <span><?php echo $item['label']; ?></span>
                                <span><?php echo number_format($item['amount'], 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($data['receipts'])): ?>
                                <div style="text-align: center; color: #999;">- Tiada Terimaan -</div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="col-nota"></td>
                    <td class="col-total"></td>
                </tr>
                <tr>
                    <td class="col-label bold">JUMLAH TERIMAAN</td>
                    <td class="col-box"></td>
                    <td class="col-nota"></td>
                    <td class="col-total">
                        <span class="amount-underline">
                            <?php echo number_format($data['total_receipts'], 2); ?>
                        </span>
                    </td>
                </tr>

                <!-- Payments -->
                <tr>
                    <td colspan="4" class="bold" style="padding-top: 15px;">
                        B. BAYARAN
                    </td>
                </tr>
                <tr>
                    <td class="col-label"></td>
                    <td class="col-box">
                        <div class="box-container">
                            <?php foreach ($data['payments'] as $item): ?>
                            <div class="box-item">
                                <span><?php echo $item['label']; ?></span>
                                <span><?php echo number_format($item['amount'], 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($data['payments'])): ?>
                                <div style="text-align: center; color: #999;">- Tiada Bayaran -</div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="col-nota"></td>
                    <td class="col-total"></td>
                </tr>
                <tr>
                    <td class="col-label bold">JUMLAH BAYARAN</td>
                    <td class="col-box"></td>
                    <td class="col-nota"></td>
                    <td class="col-total">
                        <span class="amount-underline">
                            <?php echo number_format($data['total_payments'], 2); ?>
                        </span>
                    </td>
                </tr>

                <!-- Surplus/Deficit -->
                <tr>
                    <td class="col-label" style="padding-top: 10px;">
                        Lebihan / (Kurangan) (A-B)
                    </td>
                    <td class="col-box"></td>
                    <td class="col-nota"></td>
                    <td class="col-total" style="padding-top: 10px;">
                        <span class="amount-underline-bold">
                            <?php echo number_format($data['surplus_deficit'], 2); ?>
                        </span>
                    </td>
                </tr>

                <!-- Closing Balance -->
                <tr>
                    <td colspan="3" style="padding-top: 15px;">
                        <div class="bold">BAKI PADA <?php echo $displayEndDate; ?></div>
                    </td>
                    <td class="col-total" style="padding-top: 15px;">
                        <span class="amount-double">
                            <?php echo number_format($data['closing_balance']['cash'] + $data['closing_balance']['bank'], 2); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div class="bold" style="text-decoration: underline; margin-top: 5px;">DIWAKILI OLEH</div>
                    </td>
                </tr>
                <tr>
                    <td class="col-label">
                        <div style="padding-top: 10px;">
                            <div class="row-item">Wang Tunai di tangan</div>
                            <div class="row-item">Wang Tunai di bank</div>
                            <div class="row-item">Pelaburan</div>
                        </div>
                    </td>
                    <td class="col-box">
                        <div class="box-container">
                            <div class="row-item text-right"><?php echo number_format($data['closing_balance']['cash'], 2); ?></div>
                            <div class="row-item text-right"><?php echo number_format($data['closing_balance']['bank'], 2); ?></div>
                            <div class="row-item text-right">0.00</div>
                        </div>
                    </td>
                    <td class="col-nota"></td>
                    <td class="col-total">
                        <div style="padding-top: 10px;">
                            <div class="row-item"></div>
                            <div class="row-item"></div>
                            <div class="row-item">
                                <span class="amount-underline">&nbsp;</span>
                            </div>
                        </div>
                        <div style="margin-top: 2px; text-align: right;">
                            <span class="amount-double-custom">
                                <?php echo number_format($data['closing_balance']['cash'] + $data['closing_balance']['bank'], 2); ?>
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-box">
                <div class="sig-title">Disediakan oleh :</div>
                <div>Nama:</div>
                <div>Jawatan:</div>
                <div>Tarikh :</div>
            </div>
            <div class="sig-box">
                <div class="sig-title">Disahkan oleh :</div>
                <div>Nama:</div>
                <div>Jawatan:</div>
                <div>Tarikh :</div>
            </div>
            <div class="sig-box">
                <div class="sig-title">Disemak oleh :</div>
                <div>Nama:</div>
                <div>Jawatan:</div>
                <div>Tarikh :</div>
            </div>
        </div>


    </div>

    <script>
        // Auto-print
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
