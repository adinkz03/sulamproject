<div class="card page-card">
    <h2>Record Death Notification</h2>
    <p>Submit a death notification. Fields marked required must be filled. An admin will verify the details.</p>

    <form method="post" action="" class="form-grid">
        <div class="form-group">
            <label for="full_name">Deceased Name <span style="color: #e74c3c;">*</span></label>
            <input type="text" id="full_name" name="full_name" class="form-control" required />
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="ic_number">IC Number</label>
                <input type="text" id="ic_number" name="ic_number" class="form-control" placeholder="e.g., 123456-12-1234" />
            </div>
            <div class="form-group">
                <label for="date_of_death">Date of Death <span style="color: #e74c3c;">*</span></label>
                <input type="date" id="date_of_death" name="date_of_death" class="form-control" required />
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="place_of_death">Place of Death</label>
                <input type="text" id="place_of_death" name="place_of_death" class="form-control" placeholder="e.g., Hospital, Home" />
            </div>
            <div class="form-group">
                <label for="cause_of_death">Cause of Death</label>
                <input type="text" id="cause_of_death" name="cause_of_death" class="form-control" placeholder="Optional" />
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="nok_name">Next of Kin (Name)</label>
                <input type="text" id="nok_name" name="nok_name" class="form-control" />
            </div>
            <div class="form-group">
                <label for="nok_phone">Next of Kin (Phone)</label>
                <input type="text" id="nok_phone" name="nok_phone" class="form-control" placeholder="e.g., 01234567890" />
            </div>
        </div>

        <div class="form-group full-width">
            <button class="btn btn-primary" type="submit">Submit Notification</button>
        </div>
    </form>
</div>
