<div class="card page-card">
    <h2>Funeral Logistics</h2>
    <p>Plan and track funeral logistics for verified notifications.</p>

    <?php if (empty($funeralLogistics)): ?>
        <div class="empty-state">
            <p>No funeral logistics recorded yet.</p>
        </div>
    <?php else: ?>
        <div class="grid-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <?php foreach ($funeralLogistics as $logistics): ?>
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <h3 style="margin: 0;">
                            <?php echo htmlspecialchars($logistics['deceased_name'] ?? 'Funeral Arrangement'); ?>
                        </h3>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn btn-primary btn-sm" onclick="editLogistics(<?php echo $logistics['id']; ?>)" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteLogistics(<?php echo $logistics['id']; ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Burial Date</label>
                        <div class="form-control-static">
                            <?php echo $logistics['burial_date'] ? htmlspecialchars($logistics['burial_date']) : '<span style="color: var(--muted);">Not set</span>'; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Burial Location</label>
                        <div class="form-control-static">
                            <?php echo $logistics['burial_location'] ? htmlspecialchars($logistics['burial_location']) : '<span style="color: var(--muted);">Not set</span>'; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Grave Number</label>
                        <div class="form-control-static">
                            <?php echo htmlspecialchars($logistics['grave_number'] ?? '-'); ?>
                        </div>
                    </div>

                    <?php if (!empty($logistics['notes'])): ?>
                        <div class="form-group" style="padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                            <label>Notes</label>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">
                                <?php echo nl2br(htmlspecialchars($logistics['notes'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <div style="padding-top: 0.75rem; border-top: 1px solid var(--border-color); margin-top: 1rem; color: var(--muted); font-size: 0.8rem;">
                        <strong>Created:</strong> <?php echo htmlspecialchars($logistics['created_at'] ?? 'N/A'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
