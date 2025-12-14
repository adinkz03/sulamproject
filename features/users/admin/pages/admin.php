<?php
// Moved from /admin.php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
initSecureSession();
requireAdmin();

// Simple list of users with edit links
$users = [];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role = isset($_GET['role']) ? trim($_GET['role']) : '';

$query = "SELECT users.id, users.name, users.username, users.email, users.roles, users.is_deceased, users.income, COUNT(dependent.id) as dependent_count 
          FROM users 
          LEFT JOIN dependent ON users.id = dependent.user_id";

$params = [];
$types = "";
$whereClauses = [];

if (!empty($search)) {
    $whereClauses[] = "(users.name LIKE ? OR users.username LIKE ? OR users.email LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if (!empty($role)) {
    $whereClauses[] = "users.roles = ?";
    $params[] = $role;
    $types .= "s";
}

if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$query .= " GROUP BY users.id ORDER BY users.id DESC";

$stmt = $mysqli->prepare($query);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
}

// 1. Capture the inner content
ob_start();
?>
<link rel="stylesheet" href="/features/users/admin/assets/css/users.css">
<link rel="stylesheet" href="/features/shared/assets/css/cards.css">

<div class="small-card" style="max-width:1100px;margin:0 auto;">
  <h2>Manage Users</h2>

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
                    <input type="text" name="search" placeholder="Search name, username, email..." value="<?php echo htmlspecialchars($search); ?>" class="form-input" style="width: 100%;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #64748b; margin-bottom: 0.5rem;">Role</label>
                    <select name="role" class="form-input" style="width: 100%;">
                        <option value="">All Roles</option>
                        <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

  <div id="usersListContainer">
  <?php if (empty($users)): ?>
    <div class="empty-state" style="text-align: center; padding: 2rem; color: #666;">
        <p>No users found matching your criteria.</p>
    </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover table--users">
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Income Class</th><th class="table__cell--numeric">Dependents</th><th>Deceased?</th><th class="table__cell--actions">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?php echo (int)$u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['name']); ?></td>
            <td><?php echo htmlspecialchars($u['username']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo htmlspecialchars($u['roles']); ?></td>
            <td>
              <?php
                  $income = $u['income'];
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
            <td class="table__cell--numeric"><?php echo (int)$u['dependent_count']; ?></td>
            <td><?php echo $u['is_deceased'] ? 'Yes' : 'No'; ?></td>
            <td class="table__cell--actions"><a class="btn" href="<?php echo url('admin/user-edit?id=' . (int)$u['id']); ?>">Edit</a></td>
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
<?php
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Admin - Users';
include $ROOT . '/features/shared/components/layouts/base.php';
?>
