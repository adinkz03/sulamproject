<div class="card page-card">
    <h2>Verify Death</h2>
    <p style="color: var(--muted);">Verify reported deaths and record verification details.</p>

    <div class="notice">
        Select an unverified notification and provide verifier name and notes.
    </div>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            No death notifications recorded yet.
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                <thead style="background-color: var(--bg-secondary); border-bottom: 2px solid var(--border-color);">
                    <tr>
                        <th style="padding: 0.75rem; text-align: left;">Deceased Name</th>
                        <th style="padding: 0.75rem; text-align: left;">IC Number</th>
                        <th style="padding: 0.75rem; text-align: left;">Date of Death</th>
                        <th style="padding: 0.75rem; text-align: left;">Place of Death</th>
                        <th style="padding: 0.75rem; text-align: left;">Next of Kin</th>
                        <th style="padding: 0.75rem; text-align: left;">Status</th>
                        <th style="padding: 0.75rem; text-align: left;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($item->deceased_name ?? ''); ?></td>
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($item->ic_number ?? '-'); ?></td>
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($item->date_of_death ?? ''); ?></td>
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($item->place_of_death ?? '-'); ?></td>
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($item->next_of_kin_name ?? '-'); ?></td>
                            <td style="padding: 0.75rem;">
                                <?php if ($item->verified): ?>
                                    <span style="background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">Verified</span>
                                <?php else: ?>
                                    <span style="background-color: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if (!$item->verified): ?>
                                    <button class="btn-small" onclick="verifyNotification(<?php echo $item->id; ?>)">Verify</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
