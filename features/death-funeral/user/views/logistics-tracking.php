<div class="card page-card">
    <h2>Funeral Logistics</h2>
    <p style="color: var(--muted);">View and track funeral logistics arranged for death notifications.</p>

    <?php if (empty($funeralLogistics)): ?>
        <div class="empty-state" style="padding: 2rem; text-align: center; color: var(--muted);">
            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p>No funeral logistics scheduled yet or pending verification of notifications.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <?php foreach ($funeralLogistics as $logistics): ?>
                <div class="card" style="border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="margin-top: 0; margin-bottom: 1rem;">Funeral Arrangement</h3>
                    
                    <div style="margin-bottom: 0.75rem;">
                        <strong style="display: block; color: var(--text-secondary); font-size: 0.85rem;">Burial Date</strong>
                        <span><?php echo htmlspecialchars($logistics['burial_date'] ?? 'TBD'); ?></span>
                    </div>
                    
                    <div style="margin-bottom: 0.75rem;">
                        <strong style="display: block; color: var(--text-secondary); font-size: 0.85rem;">Burial Location</strong>
                        <span><?php echo htmlspecialchars($logistics['burial_location'] ?? 'TBD'); ?></span>
                    </div>
                    
                    <div style="margin-bottom: 0.75rem;">
                        <strong style="display: block; color: var(--text-secondary); font-size: 0.85rem;">Grave Number</strong>
                        <span><?php echo htmlspecialchars($logistics['grave_number'] ?? '-'); ?></span>
                    </div>
                    
                    <?php if (!empty($logistics['notes'])): ?>
                        <div style="margin-bottom: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                            <strong style="display: block; color: var(--text-secondary); font-size: 0.85rem;">Notes</strong>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($logistics['notes'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div style="padding-top: 0.75rem; border-top: 1px solid var(--border-color); margin-top: 1rem; color: var(--muted); font-size: 0.8rem;">
                        Arranged by: Admin
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
