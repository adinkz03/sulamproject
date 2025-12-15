<?php
// Capture Left Column: Profile Edit
ob_start();
?>
    <div class="card">
        <div class="profile-card-header">
            <h3>Edit Profile</h3>
        </div>
        <div class="card-body">
            
            <?php if (isset($success)): ?>
                <div class="notice success" style="margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="notice error" style="margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="grid-2">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" disabled style="background-color: rgba(0,0,0,0.05); cursor: not-allowed;">
                    <small>Username cannot be changed.</small>
                </div>

                <div class="form-group">
                    <label for="name">Full Name <span style="color:var(--danger, red);">*</span></label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span style="color:var(--danger, red);">*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                </div>

                <?php if (!$isAdmin): ?>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="housing_status">Housing Status</label>
                    <select id="housing_status" name="housing_status">
                        <option value="">Select Housing Status</option>
                        <option value="renting" <?php echo ($user['housing_status'] ?? '') === 'renting' ? 'selected' : ''; ?>>
                            Renting
                        </option>
                        <option value="own_house" <?php echo ($user['housing_status'] ?? '') === 'own_house' ? 'selected' : ''; ?>>
                            Own House
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="marital_status">Marital Status</label>
                    <select id="marital_status" name="marital_status">
                        <option value="">Select Status</option>
                        <?php 
                        $statuses = ['single', 'married', 'divorced', 'widowed', 'others'];
                        foreach ($statuses as $status): 
                            $selected = ($user['marital_status'] ?? '') === $status ? 'selected' : '';
                        ?>
                            <option value="<?php echo $status; ?>" <?php echo $selected; ?>>
                                <?php echo ucfirst($status); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="income_range">Monthly Income Range</label>
                    <select id="income_range" name="income_range">
                        <option value="">Select Income Range</option>
                        <option value="below_5250" <?php echo ($user['income_range'] ?? '') === 'below_5250' ? 'selected' : ''; ?>>
                            Below RM5,250
                        </option>
                        <option value="between_5250_11820" <?php echo ($user['income_range'] ?? '') === 'between_5250_11820' ? 'selected' : ''; ?>>
                            RM5,250 - RM11,820
                        </option>
                        <option value="above_11820" <?php echo ($user['income_range'] ?? '') === 'above_11820' ? 'selected' : ''; ?>>
                            Above RM11,820
                        </option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="actions" style="grid-column: 1 / -1; justify-content: flex-start; margin-top: 1.5rem;">
                    <button class="btn btn-primary" type="submit" style="font-size: 1rem; padding: 0.75rem 1.5rem;">
                    <i class="fa-solid fa-save"></i>
                    Save Changes
                    </button>
                    <a class="btn outline" href="<?php echo url('dashboard'); ?>" style="font-size: 1rem; padding: 0.75rem 1.5rem;">
                    <i class="fa-solid fa-times"></i>
                    Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php
$splitLayoutLeft = ob_get_clean();

// Capture Right Column: Dependents & Next of Kin (only for non-admin users)
if (!$isAdmin) {
    ob_start();
?>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Dependents Section -->
        <div class="card">
            <div class="profile-card-header">
                <h3>Family Dependents (Tanggungan)</h3>
                <a href="<?php echo url('features/users/waris/user/pages/dependent-form.php'); ?>" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Add
                </a>
            </div>
            <div class="card-body">

                <?php if (empty($dependents)): ?>
                    <p class="text-muted">No dependents added yet.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid #eee;">
                                    <th style="padding: 0.5rem;">Name</th>
                                    <th style="padding: 0.5rem;">Relationship</th>
                                    <th style="padding: 0.5rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dependents as $dep): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 0.5rem;"><?php echo htmlspecialchars($dep['name']); ?></td>
                                        <td style="padding: 0.5rem;"><?php echo htmlspecialchars($dep['relationship'] ?? '-'); ?></td>
                                        <td style="padding: 0.5rem;">
                                            <a href="<?php echo url('features/users/waris/user/pages/dependent-form.php?id=' . $dep['id']); ?>" class="btn-text" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form action="<?php echo url('features/users/waris/user/pages/dependent-delete.php'); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="id" value="<?php echo $dep['id']; ?>">
                                                <button type="submit" class="btn-text text-danger" style="background:none; border:none; cursor:pointer;" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Next of Kin Section -->
        <div class="card">
            <div class="profile-card-header">
                <h3>Next of Kin (Waris)</h3>
                <a href="<?php echo url('features/users/waris/user/pages/next-of-kin-form.php'); ?>" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Add
                </a>
            </div>
            <div class="card-body">

                <?php if (empty($nextOfKin)): ?>
                    <p class="text-muted">No next of kin added yet.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid #eee;">
                                    <th style="padding: 0.5rem;">Name</th>
                                    <th style="padding: 0.5rem;">Relationship</th>
                                    <th style="padding: 0.5rem;">Contact</th>
                                    <th style="padding: 0.5rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($nextOfKin as $kin): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 0.5rem;"><?php echo htmlspecialchars($kin['name']); ?></td>
                                        <td style="padding: 0.5rem;"><?php echo htmlspecialchars($kin['relationship'] ?? '-'); ?></td>
                                        <td style="padding: 0.5rem; font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($kin['phone_number'] ?? '-'); ?>
                                        </td>
                                        <td style="padding: 0.5rem;">
                                            <a href="<?php echo url('features/users/waris/user/pages/next-of-kin-form.php?id=' . $kin['id']); ?>" class="btn-text" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form action="<?php echo url('features/users/waris/user/pages/next-of-kin-delete.php'); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="id" value="<?php echo $kin['id']; ?>">
                                                <button type="submit" class="btn-text text-danger" style="background:none; border:none; cursor:pointer;" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<?php
    $splitLayoutRight = ob_get_clean();
    // Use the shared split layout for regular users
    include dirname(__DIR__, 4) . '/features/shared/components/layouts/split-content-layout.php';
} else {
    // For admin users, show centered single column layout
    echo '<div style="max-width: 800px; margin: 0 auto;">';
    echo $splitLayoutLeft;
    echo '</div>';
}
?>


