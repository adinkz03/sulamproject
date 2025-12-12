<?php
/**
 * Payment Add/Edit Form View
 * Variables expected: $record (null for add), $categoryColumns, $categoryLabels, $errors, $old
 */

$isEdit = !empty($record);
$formData = $isEdit ? $record : ($old ?? []);
?>
<div class="card page-card">
    <div class="card-header">
        <h3><?php echo $isEdit ? 'Edit Payment Record' : 'Add New Payment Record'; ?></h3>
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
            <!-- Row 1: Voucher Number and Date -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <!-- Voucher Number -->
                <div class="form-group">
                    <label for="voucher_number">No. Baucar (Voucher Number)</label>
                    <input type="text" id="voucher_number" name="voucher_number" class="form-control" 
                           value="<?php echo htmlspecialchars($nextVoucherNumber ?? $formData['voucher_number'] ?? ''); ?>"
                           readonly>
                    <small class="form-text text-muted">Auto-generated voucher number</small>
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label for="tx_date">Tarikh (Date) <span style="color: red;">*</span></label>
                    <input type="date" id="tx_date" name="tx_date" class="form-control" required 
                           value="<?php echo htmlspecialchars($formData['tx_date'] ?? date('Y-m-d')); ?>">
                </div>
            </div>

            <!-- Row 2: Paid To (Full Width) -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="paid_to">Dibayar Kepada (Paid To) <span style="color: red;">*</span></label>
                <input type="text" id="paid_to" name="paid_to" class="form-control" required 
                       placeholder="e.g. Tenaga Nasional Berhad"
                       value="<?php echo htmlspecialchars($formData['paid_to'] ?? ''); ?>">
            </div>

            <!-- Row 3: Description (Full Width) -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="description">Butiran (Description) <span style="color: red;">*</span></label>
                <input type="text" id="description" name="description" class="form-control" required 
                       placeholder="e.g. Bayaran Bil Elektrik"
                       value="<?php echo htmlspecialchars($formData['description'] ?? ''); ?>">
            </div>

            <!-- Row 4: Payment Method and Reference Number -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <!-- Payment Method -->
                <div class="form-group">
                    <label for="payment_method">Kaedah Pembayaran (Payment Method) <span style="color: red;">*</span></label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="">-- Select Method --</option>
                        <option value="cash" <?php echo ($formData['payment_method'] ?? '') === 'cash' ? 'selected' : ''; ?>>Tunai (Cash)</option>
                        <option value="cheque" <?php echo ($formData['payment_method'] ?? '') === 'cheque' ? 'selected' : ''; ?>>Bank (Cek)</option>
                        <option value="bank" <?php echo ($formData['payment_method'] ?? '') === 'bank' ? 'selected' : ''; ?>>Bank (E-Banking)</option>
                    </select>
                </div>

                <!-- Payment Reference -->
                <div class="form-group" id="reference-field">
                    <label for="payment_reference">No. Rujukan (Reference Number)</label>
                    <input type="text" id="payment_reference" name="payment_reference" class="form-control" 
                           placeholder="e.g. Reference No."
                           value="<?php echo htmlspecialchars($formData['payment_reference'] ?? ''); ?>">
                    <small class="form-text text-muted">Required for Bank/Cheque.</small>
                </div>
            </div>

            <!-- Bank Details Section (Hidden by default, shown for bank/cheque) -->
            <div id="bank-details-section" style="display: none;">
                <h4 style="margin-bottom: 1rem; color: #666; font-size: 1rem;">Bank Details</h4>
                
                <!-- Row: IC Number and Bank Name -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Payee IC Number -->
                    <div class="form-group">
                        <label for="payee_ic">No. Kad Pengenalan (IC Number)</label>
                        <input type="text" id="payee_ic" name="payee_ic" class="form-control" 
                               placeholder="e.g. 123456-12-1234"
                               value="<?php echo htmlspecialchars($formData['payee_ic'] ?? ''); ?>">
                    </div>

                    <!-- Payee Bank Name -->
                    <div class="form-group">
                        <label for="payee_bank_name">Nama Bank (Bank Name)</label>
                        <input type="text" id="payee_bank_name" name="payee_bank_name" class="form-control" 
                               placeholder="e.g. Maybank, CIMB"
                               value="<?php echo htmlspecialchars($formData['payee_bank_name'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Row: Bank Account Number (Full Width) -->
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="payee_bank_account">No. Akaun (Account Number)</label>
                    <input type="text" id="payee_bank_account" name="payee_bank_account" class="form-control" 
                           placeholder="e.g. 1234567890"
                           value="<?php echo htmlspecialchars($formData['payee_bank_account'] ?? ''); ?>">
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
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update Record' : 'Save Record'; ?>
                </button>
                <a href="<?php echo url('financial/payment-account'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        <script>
            // Toggle bank details visibility based on payment method
            document.addEventListener('DOMContentLoaded', function() {
                const paymentMethodSelect = document.getElementById('payment_method');
                const bankDetailsSection = document.getElementById('bank-details-section');
                const referenceField = document.getElementById('reference-field');

                function toggleBankDetails() {
                    const selectedMethod = paymentMethodSelect.value;
                    
                    if (selectedMethod === 'bank' || selectedMethod === 'cheque') {
                        bankDetailsSection.style.display = 'block';
                        referenceField.style.display = 'block';
                    } else if (selectedMethod === 'cash') {
                        bankDetailsSection.style.display = 'none';
                        referenceField.style.display = 'none';
                    } else {
                        // No method selected yet
                        bankDetailsSection.style.display = 'none';
                        referenceField.style.display = 'none';
                    }
                }

                // Run on page load
                toggleBankDetails();

                // Run when payment method changes
                paymentMethodSelect.addEventListener('change', toggleBankDetails);
            });

            // Category management
            document.addEventListener('DOMContentLoaded', function() {
                const categoryEntriesContainer = document.getElementById('category-entries');
                const addCategoryBtn = document.getElementById('add-category');
                const form = document.querySelector('form');

                // Add new category entry
                addCategoryBtn.addEventListener('click', function() {
                    const firstEntry = categoryEntriesContainer.querySelector('.category-entry');
                    const newEntry = firstEntry.cloneNode(true);
                    
                    // Reset values
                    newEntry.querySelector('.category-select').value = '';
                    newEntry.querySelector('.category-amount').value = '';
                    
                    // Show remove button
                    newEntry.querySelector('.remove-category').style.display = 'inline-block';
                    
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
