<div id="editLogisticsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0;">Edit Funeral Logistics</h2>
            <button type="button" class="btn btn-link" onclick="closeEditModal()" style="padding: 0; color: var(--muted); background: none; border: none; font-size: 1.5rem; cursor: pointer;">
                ×
            </button>
        </div>
        
        <form id="editLogisticsForm" method="POST">
            <input type="hidden" id="logisticsId" name="id">
            
            <div class="form-group">
                <label for="editBurialDate" class="form-label">Burial Date <span class="text-danger">*</span></label>
                <input type="date" id="editBurialDate" name="burial_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="editBurialLocation" class="form-label">Burial Location <span class="text-danger">*</span></label>
                <input type="text" id="editBurialLocation" name="burial_location" class="form-control" placeholder="e.g., Cemetery Name" required>
            </div>

            <div class="form-group">
                <label for="editGraveNumber" class="form-label">Grave Number</label>
                <input type="text" id="editGraveNumber" name="grave_number" class="form-control" placeholder="e.g., Section A, Plot 12">
            </div>

            <div class="form-group">
                <label for="editNotes" class="form-label">Notes</label>
                <textarea id="editNotes" name="notes" class="form-control" rows="4" placeholder="Additional arrangements or notes..."></textarea>
            </div>

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Logistics</button>
            </div>
        </form>
    </div>
</div>
