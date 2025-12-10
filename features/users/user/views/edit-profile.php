<div class="profile-container">
    <!-- Left Column: Profile Edit -->
    <div class="card small-card profile-card">
        <h3>Edit Profile</h3>
        
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

        <form method="post" class="form-grid">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" disabled class="form-control" style="background-color: #f5f5f5; cursor: not-allowed;">
                <small class="text-muted">Username cannot be changed.</small>
            </div>

            <div class="form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required class="form-control">
            </div>

            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required class="form-control">
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" class="form-control">
            </div>

            <div class="form-group full-width">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3" class="form-control"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="marital_status">Marital Status</label>
                <select id="marital_status" name="marital_status" class="form-control">
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
                <select id="income_range" name="income_range" class="form-control">
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

            <div class="form-actions full-width" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?php echo url('dashboard'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Right Column: Dependents & Next of Kin -->
    <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Dependents Card -->
        <div class="card small-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Family Dependents</h3>
                <a href="<?php echo url('features/users/waris/user/pages/dependent-form.php'); ?>" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Add
                </a>
            </div>

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

        <!-- Next of Kin Card -->
        <div class="card small-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Next of Kin (Emergency)</h3>
                <a href="<?php echo url('features/users/waris/user/pages/next-of-kin-form.php'); ?>" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Add
                </a>
            </div>

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
</div>

<style>
    .profile-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .profile-card {
        flex: 1;
        min-width: 300px;
    }
    
    .dependents-card {
        flex: 1;
        min-width: 300px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .full-width {
        grid-column: 1 / -1;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .form-control {
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }
    .text-muted {
        color: #666;
        font-size: 0.85rem;
    }
    .required {
        color: red;
    }
    @media (max-width: 900px) {
        .profile-container {
            flex-direction: column;
        }
    }
    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
