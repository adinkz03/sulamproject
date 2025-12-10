<div class="card page-card">
    <h2>Record Death Notification</h2>
    <p style="color: var(--muted);">Submit a death notification. Fields marked required must be filled. An admin will verify the details.</p>

    <form method="post" action="" class="form-grid">
        <div class="form-group">
            <label class="form-label">Deceased Name <span style="color: #e74c3c;">*</span></label>
            <input name="full_name" class="form-input" required />
        </div>
        <div class="form-group">
            <label class="form-label">IC Number</label>
            <input name="ic_number" class="form-input" placeholder="e.g., 123456-12-1234" />
        </div>
        <div class="form-group">
            <label class="form-label">Date of Death <span style="color: #e74c3c;">*</span></label>
            <input name="date_of_death" type="date" class="form-input" required />
        </div>
        <div class="form-group">
            <label class="form-label">Place of Death</label>
            <input name="place_of_death" class="form-input" placeholder="e.g., Hospital, Home" />
        </div>
        <div class="form-group">
            <label class="form-label">Cause of Death</label>
            <input name="cause_of_death" class="form-input" placeholder="Optional" />
        </div>
        <div class="form-group">
            <label class="form-label">Next of Kin (Name)</label>
            <input name="nok_name" class="form-input" />
        </div>
        <div class="form-group">
            <label class="form-label">Next of Kin (Phone)</label>
            <input name="nok_phone" class="form-input" placeholder="e.g., 01234567890" />
        </div>
        <div class="form-group full-width">
            <button class="btn-primary" type="submit">Submit Notification</button>
        </div>
    </form>
</div>
