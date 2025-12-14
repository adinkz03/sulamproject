<?php
/**
 * Deposit Account Listing View
 * Variables expected: $deposits, $categoryColumns, $categoryLabels
 */

// Format amount for display
function formatDepositAmount($value) {
    if ($value > 0) {
        return 'RM ' . number_format($value, 2);
    }
    return '-';
}
?>

<div class="content-container">
    <!-- Deposit Account Table -->
    
    <!-- Balance Summary Stat Cards -->
    <div class="stat-cards">
        <div class="stat-card stat-card--cash">
            <div class="stat-card__label">Jum. Terimaan Tunai (Total Cash)</div>
            <div class="stat-card__value">RM <?php echo number_format($totalCash, 2); ?></div>
        </div>
        <div class="stat-card stat-card--bank">
            <div class="stat-card__label">Jum. Terimaan Bank (Total Bank)</div>
            <div class="stat-card__value">RM <?php echo number_format($totalBank, 2); ?></div>
        </div>
        <div class="stat-card stat-card--total">
            <div class="stat-card__label">Jumlah Keseluruhan (Grand Total)</div>
            <div class="stat-card__value">RM <?php echo number_format($totalCash + $totalBank, 2); ?></div>
        </div>
    </div>

    <!-- Filter Card (Styleguide Pattern) -->
    <div class="card card--filter mb-4" id="depositFilter">
        <!-- Filter Header -->
        <div class="filter-header">
            <div class="filter-icon">
                <i class="fas fa-sliders-h"></i>
            </div>
            <h4 class="filter-title">Deposit Filters</h4>
            <button type="button" class="filter-collapse-toggle" aria-label="Toggle filters" onclick="toggleDepositFilter()">
                <i class="fas fa-chevron-down" id="depositFilterIcon"></i>
            </button>
        </div>

        <!-- Active Filters Display (Pills) -->
        <?php 
        $hasFilters = !empty($_GET['date_from']) || !empty($_GET['date_to']) || !empty($_GET['payment_method']) || !empty($_GET['search']) || !empty($_GET['categories']);
        if ($hasFilters): 
        ?>
        <div style="padding: 1rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <div class="filter-pills">
                <?php if (!empty($_GET['date_from']) || !empty($_GET['date_to'])): ?>
                <span class="filter-pill filter-pill--selected">
                    <span>Date: <?php echo htmlspecialchars($_GET['date_from'] ?? 'Start'); ?> to <?php echo htmlspecialchars($_GET['date_to'] ?? 'End'); ?></span>
                    <button type="button" class="filter-pill-remove" onclick="removeDepositFilter('date')" aria-label="Remove date filter">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                <?php endif; ?>
                
                <?php if (!empty($_GET['payment_method'])): ?>
                <span class="filter-pill filter-pill--selected">
                    <span>Method: <?php echo htmlspecialchars(ucfirst($_GET['payment_method'])); ?></span>
                    <button type="button" class="filter-pill-remove" onclick="removeDepositFilter('payment_method')" aria-label="Remove payment method filter">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                <?php endif; ?>
                
                <?php if (!empty($_GET['categories'])): ?>
                    <?php foreach ($_GET['categories'] as $cat): ?>
                        <?php if (isset($categoryLabels[$cat])): ?>
                        <span class="filter-pill filter-pill--selected">
                            <span><?php echo htmlspecialchars($categoryLabels[$cat]); ?></span>
                            <button type="button" class="filter-pill-remove" onclick="removeDepositCategoryFilter('<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>')" aria-label="Remove category filter">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (!empty($_GET['search'])): ?>
                <span class="filter-pill filter-pill--selected">
                    <span>Search: "<?php echo htmlspecialchars($_GET['search']); ?>"</span>
                    <button type="button" class="filter-pill-remove" onclick="removeDepositFilter('search')" aria-label="Remove search filter">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filter Content (Collapsible) -->
        <div id="depositFilterContent" style="display: none;">
            <form method="GET" id="depositFilterForm">
                <!-- Date Range Filter Section -->
                <div class="filter-section">
                    <div class="filter-section-header" onclick="toggleDepositSection(this)">
                        <span class="filter-section-title">Date Range</span>
                        <i class="fas fa-chevron-down filter-section-icon" style="transform: rotate(-90deg);"></i>
                    </div>
                    <div class="filter-section-content" style="display: none;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem; color: var(--text-primary);">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>" style="width: 100%;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem; color: var(--text-primary);">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>" style="width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Filter Section -->
                <div class="filter-section">
                    <div class="filter-section-header" onclick="toggleDepositSection(this)">
                        <span class="filter-section-title">Payment Method</span>
                        <i class="fas fa-chevron-down filter-section-icon" style="transform: rotate(-90deg);"></i>
                    </div>
                    <div class="filter-section-content" style="display: none;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <select name="payment_method" class="form-control" style="width: 100%;">
                                <option value="">All Methods</option>
                                <option value="cash" <?php echo (isset($_GET['payment_method']) && $_GET['payment_method'] === 'cash') ? 'selected' : ''; ?>>Cash</option>
                                <option value="bank" <?php echo (isset($_GET['payment_method']) && $_GET['payment_method'] === 'bank') ? 'selected' : ''; ?>>Bank</option>
                                <option value="cheque" <?php echo (isset($_GET['payment_method']) && $_GET['payment_method'] === 'cheque') ? 'selected' : ''; ?>>Cheque</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Category Filter Section -->
                <div class="filter-section">
                    <div class="filter-section-header" onclick="toggleDepositSection(this)">
                        <span class="filter-section-title">Receipt Category</span>
                        <i class="fas fa-chevron-down filter-section-icon" style="transform: rotate(-90deg);"></i>
                    </div>
                    <div class="filter-section-content" style="display: none;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                            <?php foreach ($categoryLabels as $col => $label): ?>
                            <div>
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($col); ?>" 
                                        <?php echo (isset($_GET['categories']) && in_array($col, $_GET['categories'])) ? 'checked' : ''; ?>
                                        style="width: 16px; height: 16px; accent-color: var(--accent);">
                                    <span style="font-size: 0.875rem; color: var(--text-primary);"><?php echo htmlspecialchars($label); ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e2e8f0; font-size: 0.813rem; color: var(--muted);">
                            <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                            Shows records with non-zero values in selected categories
                        </div>
                    </div>
                </div>

                <!-- Search Filter Section -->
                <div class="filter-section">
                    <div class="filter-section-header" onclick="toggleDepositSection(this)">
                        <span class="filter-section-title">Search</span>
                        <i class="fas fa-chevron-down filter-section-icon" style="transform: rotate(-90deg);"></i>
                    </div>
                    <div class="filter-section-content" style="display: none;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem; color: var(--text-primary);">Receipt No. / Description / Received From</label>
                            <input 
                                type="text" 
                                name="search" 
                                class="form-control" 
                                placeholder="Search by receipt number, description or received from..." 
                                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                style="width: 100%;">
                        </div>
                    </div>
                </div>

                <!-- Filter Actions -->
                <div style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 0.75rem;">
                    <button type="submit" class="btn" style="flex: 1;">
                        <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Apply Filters
                    </button>
                    <button type="button" class="btn outline" onclick="resetDepositFilters()" style="flex: 1;">
                        <i class="fas fa-redo" style="margin-right: 0.5rem;"></i>Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Toggle filter card collapse
    function toggleDepositFilter() {
        const content = document.getElementById('depositFilterContent');
        const icon = document.getElementById('depositFilterIcon');
        
        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            content.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }

    // Toggle individual filter sections
    function toggleDepositSection(header) {
        const content = header.nextElementSibling;
        const icon = header.querySelector('.filter-section-icon');
        
        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.style.transform = 'rotate(0deg)';
            header.classList.remove('collapsed');
        } else {
            content.style.display = 'none';
            icon.style.transform = 'rotate(-90deg)';
            header.classList.add('collapsed');
        }
    }

    // Remove individual filter
    function removeDepositFilter(filterType) {
        const params = new URLSearchParams(window.location.search);
        
        if (filterType === 'date') {
            params.delete('date_from');
            params.delete('date_to');
        } else if (filterType === 'payment_method') {
            params.delete('payment_method');
        } else if (filterType === 'search') {
            params.delete('search');
        }
        
        window.location.href = '<?php echo url('financial/deposit-account'); ?>' + (params.toString() ? '?' + params.toString() : '');
    }

    // Remove individual category filter
    function removeDepositCategoryFilter(category) {
        const params = new URLSearchParams(window.location.search);
        const categories = params.getAll('categories[]');
        
        // Remove this category
        params.delete('categories[]');
        categories.forEach(cat => {
            if (cat !== category) {
                params.append('categories[]', cat);
            }
        });
        
        window.location.href = '<?php echo url('financial/deposit-account'); ?>' + (params.toString() ? '?' + params.toString() : '');
    }

    // Reset all filters
    function resetDepositFilters() {
        window.location.href = '<?php echo url('financial/deposit-account'); ?>';
    }
    </script>

    <?php 
    // Determine visible columns based on filter
    $selectedCategories = $_GET['categories'] ?? [];
    $visibleColumns = empty($selectedCategories) ? $categoryColumns : array_intersect($categoryColumns, $selectedCategories);
    ?>
    
    <?php if (empty($deposits)): ?>
        <div class="notice" style="text-align: center; padding: 3rem;">
            <i class="fas fa-hand-holding-usd" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
            <p style="font-size: 1.1rem; color: var(--muted);">No deposit records found. <a href="<?php echo url('financial/deposit-account/add'); ?>">Add a new record</a>.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive table-responsive--wide">
            <table class="table table-hover table--deposit-account">
                <thead>
                    <tr>
                        <th>Tarikh</th>
                        <th>No. Resit</th>
                        <th class="sticky-col-left">Butiran</th>
                        <th>Kaedah Pembayaran</th>
                        <?php foreach ($visibleColumns as $col): ?>
                            <th><?php echo htmlspecialchars($categoryLabels[$col]); ?></th>
                        <?php endforeach; ?>
                        <th>Jumlah</th>
                        <th class="table__cell--actions">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deposits as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['tx_date']); ?></td>
                        <td>
                            <?php if (!empty($row['receipt_number'])): ?>
                                <span class="badge badge-light border"><?php echo htmlspecialchars($row['receipt_number']); ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="sticky-col-left">
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($row['description'] ?? ''); ?></div>
                            <div style="color: #6b7280; font-size: 0.85em;"><?php echo htmlspecialchars($row['received_from'] ?? ''); ?></div>
                        </td>
                        <td>
                            <span class="badge badge-light border">
                                <?php echo htmlspecialchars(ucfirst($row['payment_method'] ?? 'cash')); ?>
                            </span>
                            <?php if (!empty($row['payment_reference'])): ?>
                                <div class="badge-ref" onclick="copyRef('<?php echo htmlspecialchars($row['payment_reference'], ENT_QUOTES); ?>', this)" title="Click to copy">
                                    Ref: <?php echo htmlspecialchars($row['payment_reference']); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <?php 
                        $rowTotal = 0;
                        foreach ($visibleColumns as $col): 
                            $val = (float)($row[$col] ?? 0);
                            $rowTotal += $val;
                        ?>
                            <td class="table__cell--numeric"><?php echo formatDepositAmount($val); ?></td>
                        <?php endforeach; ?>
                        <td class="table__cell--numeric" style="font-weight: bold;"><?php echo formatDepositAmount($rowTotal); ?></td>
                        <td class="table__cell--actions">
                            <?php 
                            // Build print URL with category filters
                            $printUrl = url('financial/receipt-print?id=' . $row['id']);
                            if (!empty($selectedCategories)) {
                                foreach ($selectedCategories as $cat) {
                                    $printUrl .= '&categories[]=' . urlencode($cat);
                                }
                            }
                            ?>
                            <a href="<?php echo $printUrl; ?>" class="btn btn-sm btn-outline-primary" title="Print Receipt" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            <a href="<?php echo url('financial/deposit-account/edit?id=' . $row['id']); ?>" class="btn btn-sm btn-secondary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="<?php echo url('financial/deposit-account/delete'); ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="<?php echo url('features/financial/admin/assets/css/financial.css'); ?>">

<script>
function copyRef(text, el) {
    // Navigator clipboard API
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showCopyFeedback(el);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            fallbackCopy(text, el);
        });
    } else {
        fallbackCopy(text, el);
    }
}

function fallbackCopy(text, el) {
    // Fallback using temporary textarea
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-9999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showCopyFeedback(el);
    } catch (err) {
        console.error('Fallback copy failed', err);
    }
    
    document.body.removeChild(textArea);
}

function showCopyFeedback(el) {
    const originalContent = el.innerHTML;
    // Store original style if needed
    const originalStyle = el.getAttribute('style');
    
    // Feedback style (Green/Success)
    el.style.backgroundColor = '#d1fae5'; 
    el.style.borderColor = '#34d399';
    el.style.color = '#065f46';
    el.innerHTML = '<i class="fas fa-check"></i> Copied';
    
    setTimeout(() => {
        el.innerHTML = originalContent;
        if (originalStyle) {
            el.setAttribute('style', originalStyle);
        } else {
            el.removeAttribute('style');
        }
    }, 1500);
}
</script>
