<div class="card page-card">
    <h2>Record Funeral Logistics</h2>
    <p style="color: var(--muted);">Record burial date, location and other logistics for verified notifications.</p>

    <form method="post" class="logistics-form" style="margin-top:1rem;">
        <input type="hidden" name="action" value="record_logistics">

        <div style="margin-bottom:0.75rem;">
            <label for="death_notification_id">Select Notification (Verified)</label>
            <select name="death_notification_id" id="death_notification_id" required style="width:100%; padding:0.5rem;">
                <option value="">-- Select --</option>
                <?php foreach ($items as $it): ?>
                    <?php if (!empty($it->verified)): ?>
                        <option value="<?php echo (int) $it->id; ?>"><?php echo htmlspecialchars($it->deceased_name); ?> — <?php echo htmlspecialchars($it->date_of_death); ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
            <div>
                <label for="burial_date">Burial Date</label>
                <input type="date" name="burial_date" id="burial_date" style="width:100%; padding:0.5rem;">
            </div>
            <div>
                <label for="grave_number">Grave Number</label>
                <input type="text" name="grave_number" id="grave_number" style="width:100%; padding:0.5rem;">
            </div>
        </div>

        <div style="margin-top:0.75rem;">
            <label for="burial_location">Burial Location</label>
            <input type="text" name="burial_location" id="burial_location" style="width:100%; padding:0.5rem;">
        </div>

        <div style="margin-top:0.75rem;">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" rows="4" style="width:100%; padding:0.5rem;"></textarea>
        </div>

        <div style="margin-top:1rem;">
            <button class="btn-primary" type="submit">Save Logistics</button>
        </div>
    </form>
</div>
