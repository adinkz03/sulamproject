<?php
/**
 * Cash Book View
 * Variables expected: $transactions, $tunaiBalance, $bankBalance, $openingCash, $openingBank, $fiscalYear, $hasSettings
 */
?>

<div class="content-container">
    <!-- Filter Form -->
    <div class="card card--outline mb-4">
        <div class="card-body py-4 px-4">
            <form method="GET" class="form-inline align-items-center">
                <div class="form-group mb-0 mr-4">
                    <label class="mr-2 text-muted text-uppercase small" style="font-size: 0.75rem;">Tahun</label>
                    <select name="year" class="form-control custom-select shadow-sm" style="min-width: 120px;" onchange="this.form.submit()">
                        <?php 
                        $currentYear = date('Y');
                        for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++) {
                            $selected = ($y == $fiscalYear) ? 'selected' : '';
                            echo "<option value='$y' $selected>$y</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group mb-0 mr-4">
                    <label class="mr-2 text-muted text-uppercase small" style="font-size: 0.75rem;">Bulan</label>
                    <select name="month" class="form-control custom-select shadow-sm" style="min-width: 200px;" onchange="this.form.submit()">
                        <option value="all" <?php echo ($month === null) ? 'selected' : ''; ?>>Keseluruhan Tahun</option>
                        <?php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April',
                            5 => 'Mei', 6 => 'Jun', 7 => 'Julai', 8 => 'Ogos',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
                        ];
                        foreach ($months as $num => $name) {
                            $selected = ($month === $num) ? 'selected' : '';
                            echo "<option value='$num' $selected>$num - $name</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary shadow-sm px-4">
                    <i class="fas fa-filter mr-2"></i> Tapis
                </button>
            </form>
        </div>
    </div>

    <!-- Opening Balance Alert (if not configured) -->
    <?php if (!$hasSettings): ?>
    <div class="alert alert-warning d-flex align-items-center mb-3">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <div>
            <strong>Baki awal belum dikonfigurasi.</strong> 
            Sila tetapkan baki awal untuk tahun kewangan <?php echo $fiscalYear; ?>.
            <a href="<?php echo url('financial/settings'); ?>" class="alert-link ml-2">Tetapkan Sekarang →</a>
        </div>
    </div>
    <?php endif; ?>

    <?php
    // Calculate display balances based on filtered view
    $displayCash = $openingCash;
    $displayBank = $openingBank;
    
    if (!empty($transactions)) {
        //If transactions exist, the last row represents the closing balance for this view period
        $lastTx = end($transactions);
        $displayCash = $lastTx['tunai_balance'];
        $displayBank = $lastTx['bank_balance'];
    }
    ?>

    <!-- Balance Summary Stat Cards -->
    <div class="stat-cards">
        <div class="stat-card stat-card--cash">
            <div class="stat-card__label">Baki Tunai <?php echo $month ? "($months[$month])" : "(Tahun $fiscalYear)"; ?></div>
            <div class="stat-card__value">RM <?php echo number_format($displayCash, 2); ?></div>
            <div class="stat-card__meta">Baki Awal: RM <?php echo number_format($openingCash, 2); ?></div>
        </div>
        <div class="stat-card stat-card--bank">
            <div class="stat-card__label">Baki Bank <?php echo $month ? "($months[$month])" : "(Tahun $fiscalYear)"; ?></div>
            <div class="stat-card__value">RM <?php echo number_format($displayBank, 2); ?></div>
            <div class="stat-card__meta">Baki Awal: RM <?php echo number_format($openingBank, 2); ?></div>
        </div>
        <div class="stat-card stat-card--total">
            <div class="stat-card__label">Jumlah Baki (Total Balance)</div>
            <div class="stat-card__value">RM <?php echo number_format($displayCash + $displayBank, 2); ?></div>
            <div class="stat-card__meta">Tempoh: <?php echo $month ? "$months[$month] $fiscalYear" : $fiscalYear; ?></div>
        </div>
    </div>

    <!-- Cash Book Table -->
    <div class="table-responsive">
        <table class="table table-hover table--cash-book" id="cashBookTable">
                <thead class="thead-light">
                    <tr>
                        <th rowspan="2" class="align-middle text-center" style="width: 100px;">Tarikh<br>(Date)</th>
                        <th rowspan="2" class="align-middle text-center" style="width: 120px;">No. Rujukan<br>(Ref No)</th>
                        <th rowspan="2" class="align-middle text-center">Butiran<br>(Description)</th>
                        <th colspan="2" class="text-center">Tunai (Cash)</th>
                        <th colspan="2" class="text-center">Bank</th>
                        <th colspan="2" class="text-center">Baki (Balance)</th>
                        <th rowspan="2" class="align-middle table__cell--actions" style="width: 50px;">Tindakan</th>
                    </tr>
                    <tr>
                        <th class="text-center text-success" style="width: 100px;">Masuk (In)</th>
                        <th class="text-center text-danger" style="width: 100px;">Keluar (Out)</th>
                        <th class="text-center text-success" style="width: 100px;">Masuk (In)</th>
                        <th class="text-center text-danger" style="width: 100px;">Keluar (Out)</th>
                        <th class="text-center" style="width: 100px;">Tunai</th>
                        <th class="text-center" style="width: 100px;">Bank</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Opening Balance Row -->
                    <tr class="table-secondary font-weight-bold">
                        <td class="text-center">
                            <?php 
                            // Determine opening date
                            $openDay = '01';
                            $openMonth = $month ? str_pad($month, 2, '0', STR_PAD_LEFT) : '01';
                            echo "$openDay/$openMonth/$fiscalYear";
                            ?>
                        </td>
                        <td class="text-center"><span class="badge badge-secondary">BAKI AWAL</span></td>
                        <td>Baki Bawa Ke Hadapan (Opening Balance)</td>
                        <td class="table__cell--numeric">-</td>
                        <td class="table__cell--numeric">-</td>
                        <td class="table__cell--numeric">-</td>
                        <td class="table__cell--numeric">-</td>
                        <td class="table__cell--numeric text-primary"><?php echo number_format($openingCash, 2); ?></td>
                        <td class="table__cell--numeric text-primary"><?php echo number_format($openingBank, 2); ?></td>
                        <td class="table__cell--actions">
                            <a href="<?php echo url('financial/settings'); ?>" class="btn btn-sm btn-outline-secondary" title="Edit Opening Balance">
                                <i class="fas fa-cog"></i>
                            </a>
                        </td>
                    </tr>

                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle mr-1"></i> Tiada transaksi dijumpai untuk tahun <?php echo $fiscalYear; ?>.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                            <?php 
                                $amount = (float)$tx['amount'];
                                $isCash = $tx['payment_method'] === 'cash';
                                $isIn = $tx['type'] === 'IN';
                                
                                // Determine where to place the amount
                                $cashIn = ($isIn && $isCash) ? $amount : 0;
                                $cashOut = (!$isIn && $isCash) ? $amount : 0;
                                $bankIn = ($isIn && !$isCash) ? $amount : 0;
                                $bankOut = (!$isIn && !$isCash) ? $amount : 0;
                            ?>
                            <tr>
                                <td class="text-center"><?php echo date('d/m/Y', strtotime($tx['tx_date'])); ?></td>
                                <td class="text-center">
                                    <?php if ($tx['ref_no']): ?>
                                        <span class="badge badge-light border"><?php echo htmlspecialchars($tx['ref_no']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($tx['description']); ?></td>
                                
                                <!-- Tunai Columns -->
                                <td class="table__cell--numeric text-success">
                                    <?php echo $cashIn > 0 ? number_format($cashIn, 2) : '-'; ?>
                                </td>
                                <td class="table__cell--numeric text-danger">
                                    <?php echo $cashOut > 0 ? number_format($cashOut, 2) : '-'; ?>
                                </td>
                                
                                <!-- Bank Columns -->
                                <td class="table__cell--numeric text-success">
                                    <?php echo $bankIn > 0 ? number_format($bankIn, 2) : '-'; ?>
                                </td>
                                <td class="table__cell--numeric text-danger">
                                    <?php echo $bankOut > 0 ? number_format($bankOut, 2) : '-'; ?>
                                </td>
                                
                                <!-- Balance Columns -->
                                <td class="table__cell--numeric font-weight-bold">
                                    <?php echo number_format($tx['tunai_balance'], 2); ?>
                                </td>
                                <td class="table__cell--numeric font-weight-bold">
                                    <?php echo number_format($tx['bank_balance'], 2); ?>
                                </td>

                                <!-- Actions -->
                                <td class="table__cell--actions">
                                    <?php if ($isIn): ?>
                                        <a href="<?php echo url("financial/receipt-print?id={$tx['id']}"); ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary" title="Print Receipt">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo url("financial/voucher-print?id={$tx['id']}"); ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary" title="Print Voucher">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="3" class="text-right">Baki Semasa (Current Balance):</td>
                        <td colspan="2" class="text-center text-primary">
                            RM <?php echo number_format($displayCash, 2); ?>
                        </td>
                        <td colspan="2" class="text-center text-primary">
                            RM <?php echo number_format($displayBank, 2); ?>
                        </td>
                        <td colspan="3" class="text-center">
                            <strong>Jumlah: RM <?php echo number_format($displayCash + $displayBank, 2); ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
</div>
