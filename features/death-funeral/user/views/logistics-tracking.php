<div class="card page-card">
    <h2>Funeral Logistics</h2>
    <p>View and track funeral logistics arranged for death notifications.</p>

    <?php if (empty($funeralLogistics)): ?>
        <div class="empty-state" style="padding: 2rem; text-align: center; color: var(--muted);">
            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p>No funeral logistics scheduled yet or pending verification of notifications.</p>
        </div>
    <?php else: ?>
        <div class="grid-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <?php foreach ($funeralLogistics as $logistics): ?>
                <div class="card">
                    <h3 style="margin-top: 0; margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($logistics->deceased_name ?? 'Funeral Arrangement'); ?>
                    </h3>
                    
                    <div class="form-group">
                        <label>Burial Date</label>
                        <div class="form-control-static">
                            <?php echo $logistics->burial_date ? htmlspecialchars($logistics->burial_date) : '<span style="color: var(--muted);">TBD</span>'; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Burial Location</label>
                        <div class="form-control-static">
                            <?php echo $logistics->burial_location ? htmlspecialchars($logistics->burial_location) : '<span style="color: var(--muted);">TBD</span>'; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Grave Number</label>
                        <div class="form-control-static">
                            <?php echo htmlspecialchars($logistics->grave_number ?? '-'); ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($logistics->notes)): ?>
                        <div class="form-group" style="padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                            <label>Notes</label>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">
                                <?php echo nl2br(htmlspecialchars($logistics->notes)); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <div style="padding-top: 0.75rem; border-top: 1px solid var(--border-color); margin-top: 1rem; color: var(--muted); font-size: 0.8rem;">
                        <strong>Date Arranged:</strong> <?php echo htmlspecialchars($logistics->created_at ?? 'N/A'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
