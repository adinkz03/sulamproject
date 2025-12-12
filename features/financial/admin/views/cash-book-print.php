<?php
/**
 * Cash Book Print Template (Lampiran 5 match)
 */

$months = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April',
    5 => 'Mei', 6 => 'Jun', 7 => 'Julai', 8 => 'Ogos',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
];

$monthName = $month ? $months[$month] : "Keseluruhan Tahun";
$titleDate = $month ? "$monthName $fiscalYear" : "$fiscalYear";
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Buku Tunai - <?php echo $titleDate; ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0; /* Removing margin from @page often hides browser headers/footers */
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            margin: 15mm; /* Move margin here */
            padding: 0;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 10px 0;
        }
        .lampiran-info {
            position: absolute;
            top: 0;
            right: 0;
            text-align: right;
            font-size: 9pt;
        }
        .org-info {
            border-bottom: 1px solid black;
            padding-bottom: 2px;
            margin-bottom: 5px;
            display: inline-block;
            min-width: 60%;
            text-align: center;
            font-style: italic;
        }
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .print-table th, .print-table td {
            border: 1px solid black;
            padding: 4px;
            vertical-align: middle;
        }
        .print-table th {
            background-color: #f0f0f0; /* Light gray for headers in print if color printing allowed, otherwise plain */
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
        }
        .print-table td {
            font-size: 9pt;
        }
        .col-date { width: 80px; text-align: center; }
        .col-ref { width: 80px; text-align: center; }
        .col-cek { width: 80px; text-align: center; }
        .col-desc { }
        .col-money { width: 85px; text-align: right; }
        
        .header-yellow { background-color: #ffffcc !important; } /* Approximating the yellow in screenshot */
        .header-orange { background-color: #ffcc99 !important; }
        .header-cyan { background-color: #ccffff !important; }
        .header-purple { background-color: #eebbff !important; }

        .footer-signatures {
            display: flex;
            justify-content: space-between;
            gap: 20px; /* Included gap for spacing */
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-box {
            border: 1px solid black;
            width: 30%; /* Slightly reduced width to accommodate gap */
            padding: 10px;
            min-height: 100px;
        }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; padding: 10px; background: #eee; border-bottom: 1px solid #ddd;">
        <button onclick="window.print()" style="padding: 5px 15px; font-weight: bold;">Print / Save as PDF</button>
        <button onclick="window.close()" style="padding: 5px 15px;">Close</button>
    </div>

    <div class="header">
        <h1>BUKU TUNAI</h1>
        
        <div class="org-info">
            (JAWATANKUASA PENGURUSAN MASJID DARUL ULUM)
        </div>
        <br>
        <div class="org-info" style="border-bottom: 1px solid black;">
            (Taman Desa Ilmu, 94300 Kota Samarahan, Sarawak)
        </div>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th colspan="4" style="text-align: left; background: none; border: 1px solid black;">
                    BULAN : <?php echo strtoupper($monthName) . " " . $fiscalYear; ?>
                </th>
                <th colspan="2" class="header-orange">TUNAI</th>
                <th colspan="2" class="header-cyan">BANK</th>
                <th colspan="2" class="header-purple">BAKI</th>
            </tr>
            <tr class="header-yellow">
                <th class="col-date">Tarikh</th>
                <th class="col-ref">No.<br>Resit<br>Rasmi</th>
                <th class="col-ref">No.<br>Baucar<br>Bayaran</th>
                <th class="col-desc">Perkara</th>
                <th class="col-money">Masuk<br>(RM)</th>
                <th class="col-money">Keluar<br>(RM)</th>
                <th class="col-money">Masuk<br>(RM)</th>
                <th class="col-money">Keluar<br>(RM)</th>
                <th class="col-money">TUNAI<br>(RM)</th>
                <th class="col-money">BANK<br>(RM)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Opening Balance -->
            <tr>
                <td class="col-date">
                    <?php 
                    $openDay = '01';
                    $openMonthStr = $month ? str_pad($month, 2, '0', STR_PAD_LEFT) : '01';
                    echo "$openDay/$openMonthStr/$fiscalYear";
                    ?>
                </td>
                <td class="col-ref"></td>
                <td class="col-ref"></td>
                <td style="font-weight: bold;">Baki dibawa ke hadapan</td>
                <!-- Empty In/Out columns -->
                <td class="col-money"></td>
                <td class="col-money"></td>
                <td class="col-money"></td>
                <td class="col-money"></td>
                <!-- Opening Balances -->
                <td class="col-money"><?php echo number_format($openingCash, 2); ?></td>
                <td class="col-money"><?php echo number_format($openingBank, 2); ?></td>
            </tr>

            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">Tiada transaksi.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $tx): ?>
                    <?php 
                        $amount = (float)$tx['amount'];
                        $isCash = $tx['payment_method'] === 'cash';
                        $isIn = $tx['type'] === 'IN';
                        
                        $cashIn = ($isIn && $isCash) ? $amount : 0;
                        $cashOut = (!$isIn && $isCash) ? $amount : 0;
                        $bankIn = ($isIn && !$isCash) ? $amount : 0;
                        $bankOut = (!$isIn && !$isCash) ? $amount : 0;

                        // Ref No Check
                        $refNo = $tx['ref_no'] ?? '-';
                        $isReceipt = $isIn; // Receipt usually for IN
                        $isVoucher = !$isIn; // Voucher usually for OUT
                    ?>
                    <tr>
                        <td class="col-date"><?php echo date('d/m/Y', strtotime($tx['tx_date'])); ?></td>
                        
                        <!-- No. Resit (only if IN/Receipt) -->
                        <td class="col-ref"><?php echo $isReceipt ? htmlspecialchars($refNo) : ''; ?></td>
                        
                        <!-- No. Baucar (only if OUT/Voucher) -->
                        <td class="col-ref"><?php echo $isVoucher ? htmlspecialchars($refNo) : ''; ?></td>

                        <td class="col-desc"><?php echo htmlspecialchars($tx['description']); ?></td>
                        
                        <td class="col-money"><?php echo $cashIn > 0 ? number_format($cashIn, 2) : ''; ?></td>
                        <td class="col-money"><?php echo $cashOut > 0 ? number_format($cashOut, 2) : ''; ?></td>
                        
                        <td class="col-money"><?php echo $bankIn > 0 ? number_format($bankIn, 2) : ''; ?></td>
                        <td class="col-money"><?php echo $bankOut > 0 ? number_format($bankOut, 2) : ''; ?></td>
                        
                        <td class="col-money"><?php echo number_format($tx['tunai_balance'], 2); ?></td>
                        <td class="col-money"><?php echo number_format($tx['bank_balance'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <!-- Closing Balance Row / JUMLAH Row as per Lampiran 5 -->
            <tr style="background-color: #ccc; font-weight: bold;">
                <td colspan="4">JUMLAH</td>
                <?php
                // Quickly sum up displayed totals
                $sumCashIn = 0; $sumCashOut = 0;
                $sumBankIn = 0; $sumBankOut = 0;
                foreach ($transactions as $tx) {
                     $a = (float)$tx['amount'];
                     $c = $tx['payment_method'] === 'cash';
                     $i = $tx['type'] === 'IN';
                     if ($c && $i) $sumCashIn += $a;
                     if ($c && !$i) $sumCashOut += $a;
                     if (!$c && $i) $sumBankIn += $a;
                     if (!$c && !$i) $sumBankOut += $a;
                }
                ?>
                <td class="col-money"><?php echo number_format($sumCashIn, 2); ?></td>
                <td class="col-money"><?php echo number_format($sumCashOut, 2); ?></td>
                <td class="col-money"><?php echo number_format($sumBankIn, 2); ?></td>
                <td class="col-money"><?php echo number_format($sumBankOut, 2); ?></td>
                
                <!-- Final Balances -->
                <td class="col-money" style="background-color: #aaa;"><?php echo number_format($tunaiBalance, 2); ?></td>
                <td class="col-money" style="background-color: #aaa;"><?php echo number_format($bankBalance, 2); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="footer-signatures">
        <div class="signature-box">
            <p>Disediakan oleh :</p>
            <br><br>
            Bendahari<br>
            Nama :<br>
            Tarikh :
        </div>
        <div class="signature-box">
            <p>Disahkan oleh :</p>
            <br><br>
            Pengerusi<br>
            Nama :<br>
            Tarikh :
        </div>
        <div class="signature-box">
            <p>Disemak oleh :</p>
            <br><br>
            Juruaudit Dalam<br>
            Nama :<br>
            Tarikh :
        </div>
    </div>

</body>
</html>
