<div class="card page-card">
    <h2>My Death Notifications</h2>
    <p style="color: var(--muted);">View death notifications you have submitted and their verification status.</p>

    <?php if (empty($userItems)): ?>
        <div class="empty-state" style="padding: 2rem; text-align: center; color: var(--muted);">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p>You haven't submitted any death notifications yet.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto; margin-top: 1rem;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: var(--bg-secondary); border-bottom: 2px solid var(--border-color);">
                    <tr>
                        <th style="padding: 0.75rem; text-align: left;">Deceased Name</th>
                        <th style="padding: 0.75rem; text-align: left;">Date of Death</th>
                        <th style="padding: 0.75rem; text-align: left;">Next of Kin</th>
                        <th style="padding: 0.75rem; text-align: left;">Status</th>
                        <th style="padding: 0.75rem; text-align: left;">Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userItems as $item): ?>
                        <?php if (is_object($item)): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($item->deceased_name ?? ''); ?></td>
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($item->date_of_death ?? ''); ?></td>
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($item->next_of_kin_name ?? '-'); ?></td>
                            <td style="padding: 0.75rem;">
                                <?php if (!empty($item->verified)): ?>
                                    <span style="background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; display: inline-block;">
                                        <i class="fas fa-check"></i> Verified
                                    </span>
                                <?php else: ?>
                                    <span style="background-color: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; display: inline-block;">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem; color: var(--muted); font-size: 0.875rem;">
                                <?php echo !empty($item->created_at) ? date('M d, Y', strtotime($item->created_at)) : '-'; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
