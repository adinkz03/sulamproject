<?php
echo '<div class="card page-card">';
?>
    <h2>Verified Death Notifications</h2>
    <p>Community death notifications that have been verified by administrators.</p>
<?php
    if (empty($verifiedNotifications)): ?>
        <div class="empty-state">
            <p>No verified death notifications at this time.</p>
        echo '</div>';
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Deceased Name</th>
                        <th>IC Number</th>
                        <th>Date of Death</th>
                        <th>Place of Death</th>
                        <th>Next of Kin</th>
                        <th>Verified Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($verifiedNotifications as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item->deceased_name ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item->ic_number ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($item->date_of_death ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item->place_of_death ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($item->next_of_kin_name ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($item->verified_at ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>