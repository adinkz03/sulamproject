<link rel="stylesheet" href="/features/shared/assets/css/cards.css">

<div class="content-container">
    <!-- Filter Card (Financial Style) -->
    <div class="card card--filter" style="width: 100%; margin-bottom: 1.5rem;">
        <div class="filter-header" onclick="document.getElementById('usersFilterContent').style.display = document.getElementById('usersFilterContent').style.display === 'none' ? 'block' : 'none'" style="cursor: pointer;">
            <div class="filter-icon"><i class="fas fa-filter"></i></div>
            <h4 class="filter-title" style="flex: 1;">Filter Users</h4>
            <i class="fas fa-chevron-down" style="color: #94a3b8;"></i>
        </div>
        
        <div id="usersFilterContent" style="padding: 1rem;">
            <form id="usersFilterForm" method="get" style="display: grid; grid-template-columns: 1fr auto; gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #64748b; margin-bottom: 0.5rem;">Search</label>
                    <input type="text" name="search" placeholder="Search name, username, email..." value="<?php echo htmlspecialchars($search ?? ''); ?>" class="form-input" style="width: 100%;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #64748b; margin-bottom: 0.5rem;">Role</label>
                    <select name="role" class="form-input" style="width: 100%;">
                        <option value="">All Roles</option>
                        <option value="resident" <?php echo ($currentRole ?? '') === 'resident' ? 'selected' : ''; ?>>Residents</option>
                        <option value="admin" <?php echo ($currentRole ?? '') === 'admin' ? 'selected' : ''; ?>>Admins</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div id="usersListContainer">
    <div style="color: var(--muted); font-size: 0.9rem; margin-bottom: 1rem; text-align: right;">
        <?php echo count($users); ?> user<?php echo count($users) !== 1 ? 's' : ''; ?> found
    </div>

    <!-- Users Table -->
    <?php if (empty($users)): ?>
        <div class="notice" style="text-align: center; padding: 3rem;">
            <i class="fas fa-users" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
            <p style="font-size: 1.1rem; color: var(--muted);">No users found.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Income Class</th>
                    <th>Housing Status</th>
                    <th class="table__cell--numeric">Dependents</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="table__cell--actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo e($user['name']); ?></td>
                        <td><?php echo e($user['username']); ?></td>
                        <td>
                            <span class="badge <?php echo $user['roles'] === 'admin' ? 'badge-primary' : 'badge-secondary'; ?>">
                                <?php echo e($user['roles']); ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <?php
                                $income = $user['income'];
                                $incomeClass = '-';
                                if ($income !== null && $income !== '') {
                                    if ($income < 5250) {
                                        $incomeClass = 'B40';
                                    } elseif ($income < 11820) {
                                        $incomeClass = 'M40';
                                    } else {
                                        $incomeClass = 'T20';
                                    }
                                }
                                echo $incomeClass;
                            ?>
                        </td>
                        <td style="text-align:center;">
                            <?php
                                $hs = $user['housing_status'] ?? '';
                                if ($hs === 'renting') {
                                    echo 'Renting';
                                } elseif ($hs === 'own_house') {
                                    echo 'Own House';
                                } else {
                                    echo '-';
                                }
                            ?>
                        </td>
                        <td class="table__cell--numeric" style="text-align:center;">
                            <?php echo isset($user['dependent_count']) ? $user['dependent_count'] : 0; ?>
                        </td>
                        <td><?php echo e($user['email']); ?></td>
                        <td><?php echo e($user['phone_number'] ?? '-'); ?></td>
                        <td>
                            <?php echo $user['is_deceased'] ? '<span style="color:red;">Deceased</span>' : 'Active'; ?>
                        </td>
                        <td class="table__cell--actions">
                            <?php if ($user['roles'] === 'resident'): ?>
                                <a href="/admin/waris?user_id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">View Waris</a>
                            <?php endif; ?>
                            <a href="<?php echo url('admin/user-edit?id=' . $user['id']); ?>" class="btn btn-secondary btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('usersFilterForm');
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, select');
    let timeout = null;

    function fetchResults() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const url = window.location.pathname + '?' + params.toString();
        
        const container = document.getElementById('usersListContainer');
        if (container) container.style.opacity = '0.5';
        
        fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('usersListContainer');
            
            if (newContainer && container) {
                container.innerHTML = newContainer.innerHTML;
            }
            if (container) container.style.opacity = '1';
            
            // Update URL
            window.history.pushState({}, '', url);
        })
        .catch(err => {
            console.error('Filter error:', err);
            if (container) container.style.opacity = '1';
        });
    }

    inputs.forEach(input => {
        if (input.tagName === 'SELECT') {
            input.addEventListener('change', fetchResults);
        } else {
            input.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(fetchResults, 300);
            });
        }
    });
});
</script>