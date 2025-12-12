<?php
/**
 * Deposit Add/Edit Form View
 * Variables expected: $record (null for add), $categoryColumns, $categoryLabels, $errors, $old
 */

$isEdit = !empty($record);
$formData = $isEdit ? $record : ($old ?? []);
?>
<div class="card page-card">
    <div class="card-header">
        <h3><?php echo $isEdit ? 'Edit Deposit Record' : 'Add New Deposit Record'; ?></h3>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="padding: 1rem; margin-bottom: 1rem; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <!-- Row 1: Receipt Number and Date -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <!-- Receipt Number -->
                <div class="form-group">
                    <label for="receipt_number">No. Resit (Receipt Number)</label>
                    <input type="text" id="receipt_number" name="receipt_number" class="form-control" 
                           placeholder="e.g. RR/2025/001"
                           value="<?php echo htmlspecialchars($formData['receipt_number'] ?? ''); ?>">
                    <small class="form-text text-muted">Optional. Leave blank to auto-generate later.</small>
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label for="tx_date">Tarikh (Date) <span style="color: red;">*</span></label>
                    <input type="date" id="tx_date" name="tx_date" class="form-control" required 
                           value="<?php echo htmlspecialchars($formData['tx_date'] ?? date('Y-m-d')); ?>">
                </div>
            </div>

            <!-- Row 2: Payment Method and Reference Number -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <!-- Payment Method -->
                <div class="form-group">
                    <label for="payment_method">Kaedah Pembayaran (Payment Method) <span style="color: red;">*</span></label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="">-- Select Method --</option>
                        <option value="cash" <?php echo ($formData['payment_method'] ?? '') === 'cash' ? 'selected' : ''; ?>>Tunai (Cash)</option>
                        <option value="cheque" <?php echo ($formData['payment_method'] ?? '') === 'cheque' ? 'selected' : ''; ?>>Cek (Cheque)</option>
                        <option value="bank" <?php echo ($formData['payment_method'] ?? '') === 'bank' ? 'selected' : ''; ?>>Bank Transfer</option>
                    </select>
                </div>

                <!-- Payment Reference -->
                <div class="form-group">
                    <label for="payment_reference">No. Rujukan (Reference Number)</label>
                    <input type="text" id="payment_reference" name="payment_reference" class="form-control" 
                           placeholder="e.g. TRX123456 or Cheque No."
                           value="<?php echo htmlspecialchars($formData['payment_reference'] ?? ''); ?>">
                    <small class="form-text text-muted">Required for Bank Transfer or Cheque payments.</small>
                </div>
            </div>

            <!-- Row 3: Received From and Description -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <!-- Received From -->
                <div class="form-group">
                    <label for="received_from">Diterima Dari (Received From) <span style="color: red;">*</span></label>
                    <input type="text" id="received_from" name="received_from" class="form-control" required 
                           placeholder="e.g. Ahmad bin Ali"
                           value="<?php echo htmlspecialchars($formData['received_from'] ?? ''); ?>">
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Butiran (Description) <span style="color: red;">*</span></label>
                    <input type="text" id="description" name="description" class="form-control" required 
                           placeholder="e.g. Kutipan Jumaat"
                           value="<?php echo htmlspecialchars($formData['description'] ?? ''); ?>">
                </div>
            </div>

            <!-- Category Amounts -->
            <h4 style="margin-top: 1.5rem; margin-bottom: 0.5rem;">Category Amounts (RM)</h4>
            <p class="text-muted" style="margin-bottom: 1rem; font-size: 0.9rem;">Select a category and enter the amount.</p>

            <div id="category-entries">
                <!-- Initial category entry -->
                <div class="category-entry" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: start;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Category</label>
                        <select class="form-control category-select" name="categories[]" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categoryColumns as $col): ?>
                                <option value="<?php echo $col; ?>"><?php echo htmlspecialchars($categoryLabels[$col]); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Amount (RM)</label>
                        <input type="number" class="form-control category-amount" name="amounts[]" step="0.01" min="0.01" placeholder="0.00" required>
                    </div>
                    <div style="padding-top: 28px;">
                        <button type="button" class="btn btn-danger btn-sm remove-category" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="add-category" class="btn btn-secondary btn-sm" style="margin-bottom: 1.5rem; display: none;">
                <i class="fas fa-plus"></i> Add Another Category
            </button>

            <!-- Hidden inputs for actual category data (will be populated on submit) -->
            <div id="hidden-category-inputs"></div>

            <!-- Buttons -->
            <div class="form-actions" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update Record' : 'Save Record'; ?>
                </button>
                <a href="<?php echo url('financial/deposit-account'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        <script>
            // Category management
            document.addEventListener('DOMContentLoaded', function() {
                const categoryEntriesContainer = document.getElementById('category-entries');
                const addCategoryBtn = document.getElementById('add-category');
                const form = document.querySelector('form');

                // Load existing data if editing
                <?php if ($isEdit && !empty($record)): ?>
                const existingCategories = [];
                <?php foreach ($categoryColumns as $col): ?>
                <?php if (!empty($record[$col]) && $record[$col] > 0): ?>
                existingCategories.push({
                    category: '<?php echo $col; ?>',
                    amount: <?php echo $record[$col]; ?>
                });
                <?php endif; ?>
                <?php endforeach; ?>

                // Clear initial empty entry if we have existing data
                if (existingCategories.length > 0) {
                    categoryEntriesContainer.innerHTML = '';
                    
                    existingCategories.forEach((item, index) => {
                        const entry = createCategoryEntry();
                        entry.querySelector('.category-select').value = item.category;
                        entry.querySelector('.category-amount').value = item.amount;
                        categoryEntriesContainer.appendChild(entry);
                    });
                    
                    updateRemoveButtons();
                }
                <?php endif; ?>

                // Create a new category entry element
                function createCategoryEntry() {
                    const div = document.createElement('div');
                    div.className = 'category-entry';
                    div.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: start;';
                    
                    div.innerHTML = `
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Category</label>
                            <select class="form-control category-select" name="categories[]" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categoryColumns as $col): ?>
                                    <option value="<?php echo $col; ?>"><?php echo htmlspecialchars($categoryLabels[$col]); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Amount (RM)</label>
                            <input type="number" class="form-control category-amount" name="amounts[]" step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                        <div style="padding-top: 28px;">
                            <button type="button" class="btn btn-danger btn-sm remove-category" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    
                    return div;
                }

                // Add new category entry
                addCategoryBtn.addEventListener('click', function() {
                    const newEntry = createCategoryEntry();
                    categoryEntriesContainer.appendChild(newEntry);
                    updateRemoveButtons();
                });

                // Remove category entry (using event delegation)
                categoryEntriesContainer.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-category')) {
                        e.target.closest('.category-entry').remove();
                        updateRemoveButtons();
                    }
                });

                // Update visibility of remove buttons
                function updateRemoveButtons() {
                    const entries = categoryEntriesContainer.querySelectorAll('.category-entry');
                    entries.forEach((entry, index) => {
                        const removeBtn = entry.querySelector('.remove-category');
                        if (entries.length > 1) {
                            removeBtn.style.display = 'inline-block';
                        } else {
                            removeBtn.style.display = 'none';
                        }
                    });
                }

                // Before form submit, convert category entries to proper format
                form.addEventListener('submit', function(e) {
                    const hiddenInputsContainer = document.getElementById('hidden-category-inputs');
                    hiddenInputsContainer.innerHTML = ''; // Clear previous

                    const entries = categoryEntriesContainer.querySelectorAll('.category-entry');
                    const categoryData = {};

                    // Collect all category-amount pairs
                    entries.forEach(entry => {
                        const category = entry.querySelector('.category-select').value;
                        const amount = entry.querySelector('.category-amount').value;
                        
                        if (category && amount) {
                            // If category already exists, add to it (in case of duplicates)
                            if (categoryData[category]) {
                                categoryData[category] = parseFloat(categoryData[category]) + parseFloat(amount);
                            } else {
                                categoryData[category] = parseFloat(amount);
                            }
                        }
                    });

                    // Create hidden inputs for each category
                    <?php foreach ($categoryColumns as $col): ?>
                    const input_<?php echo $col; ?> = document.createElement('input');
                    input_<?php echo $col; ?>.type = 'hidden';
                    input_<?php echo $col; ?>.name = '<?php echo $col; ?>';
                    input_<?php echo $col; ?>.value = categoryData['<?php echo $col; ?>'] || '0';
                    hiddenInputsContainer.appendChild(input_<?php echo $col; ?>);
                    <?php endforeach; ?>

                    // Remove the category-select and amount inputs from submission
                    entries.forEach(entry => {
                        entry.querySelector('.category-select').removeAttribute('name');
                        entry.querySelector('.category-amount').removeAttribute('name');
                    });
                });

                // Initial update
                updateRemoveButtons();
            });
        </script>
    </div>
</div>
