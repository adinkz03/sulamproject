<div class="card page-card">
    <h2>Verify Death</h2>
    <p>Verify reported deaths and record verification details.</p>

    <div class="alert alert-info">
        Select an unverified notification and provide verifier name and notes.
    </div>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            No death notifications recorded yet.
        </div>
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
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item->deceased_name ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item->ic_number ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($item->date_of_death ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item->place_of_death ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($item->next_of_kin_name ?? '-'); ?></td>
                            <td>
                                <?php if ($item->verified): ?>
                                    <span class="badge badge-success">Verified</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="table__cell--actions">
                                <div class="actions">
                                    <?php if (!$item->verified): ?>
                                        <button class="btn btn-primary btn-sm" onclick="verifyNotification(<?php echo $item->id; ?>)">Verify</button>
                                    <?php endif; ?>
                                    <button class="btn btn-danger btn-sm" onclick="deleteNotification(<?php echo $item->id; ?>)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
