<div class="card page-card">
    <h2>Record Funeral Logistics</h2>
    <p>Record burial date, location and other logistics for verified notifications.</p>

    <form method="post" class="form-grid mt-4">
        <input type="hidden" name="action" value="record_logistics">

        <div class="form-group full-width">
            <label class="form-label" for="death_notification_id">Select Notification (Verified)</label>
            <select name="death_notification_id" id="death_notification_id" class="form-select" required>
                <option value="">-- Select --</option>
                <?php foreach ($items as $it): ?>
                    <?php if (!empty($it->verified)): ?>
                        <option value="<?php echo (int) $it->id; ?>"><?php echo htmlspecialchars($it->deceased_name); ?> — <?php echo htmlspecialchars($it->date_of_death); ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="burial_date">Burial Date</label>
            <input type="date" name="burial_date" id="burial_date" class="form-control">
        </div>
        <div class="form-group">
            <label class="form-label" for="grave_number">Grave Number</label>
            <input type="text" name="grave_number" id="grave_number" class="form-control">
        </div>

        <div class="form-group full-width">
            <label class="form-label" for="burial_location">Burial Location</label>
            <input type="text" name="burial_location" id="burial_location" class="form-control">
        </div>

        <div class="form-group full-width">
            <label class="form-label" for="notes">Notes</label>
            <textarea name="notes" id="notes" rows="4" class="form-control"></textarea>
        </div>

        <div class="form-group full-width actions" style="text-align: right;">
            <button class="btn-primary" type="submit">Save Logistics</button>
        </div>
    </form>
</div>
