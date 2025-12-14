<div class="content-container">
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0;">Families Registry</h3>
        <button onclick="window.print()" class="btn btn-secondary btn-sm">
            <i class="fas fa-print"></i> Print List
        </button>
    </div>

    <!-- Filter Card (Financial Style) -->
    <div class="card card--filter" style="width: 100%; margin-bottom: 1.5rem;">
        <div class="filter-header" onclick="document.getElementById('familiesFilterContent').style.display = document.getElementById('familiesFilterContent').style.display === 'none' ? 'block' : 'none'" style="cursor: pointer;">
            <div class="filter-icon"><i class="fas fa-filter"></i></div>
            <h4 class="filter-title" style="flex: 1;">Filter Families</h4>
            <i class="fas fa-chevron-down" style="color: #94a3b8;"></i>
        </div>
        
        <div id="familiesFilterContent" style="padding: 1rem;">
            <form id="familiesFilterForm" method="get" style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #64748b; margin-bottom: 0.5rem;">Search</label>
                    <input type="text" name="search" placeholder="Search name, IC, address..." value="<?php echo htmlspecialchars($search ?? ''); ?>" class="form-input" style="width: 100%;">
                </div>
            </form>
        </div>
    </div>

    <div id="familiesListContainer">
    <!-- Families Table -->
    <?php if (empty($families)): ?>
        <div class="notice" style="text-align: center; padding: 3rem;">
            <i class="fas fa-users" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
            <p style="font-size: 1.1rem; color: var(--muted);">No families found matching your criteria.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover table--families">
                <thead>
                    <tr>
                        <th>Head of Family</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Dependents</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($families as $family): ?>
                        <tr>
                            <td>
                                <div style="font-weight: bold;"><?php echo htmlspecialchars($family['name']); ?></div>
                                <div class="text-muted text-sm"><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($family['username']); ?></div>
                            </td>
                            <td>
                                <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($family['phone_number'] ?? '-'); ?></div>
                                <div class="text-muted text-sm"><?php echo htmlspecialchars($family['email'] ?? ''); ?></div>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($family['address'] ?? 'No address provided'); ?>
                            </td>
                            <td>
                                <?php if (empty($family['dependents'])): ?>
                                    <span class="text-muted">No dependents</span>
                                <?php else: ?>
                                    <ul style="padding-left: 1.2rem; margin: 0;">
                                        <?php foreach ($family['dependents'] as $dep): ?>
                                            <li>
                                                <?php echo htmlspecialchars($dep['name']); ?> 
                                                <span class="badge badge-secondary badge-sm"><?php echo htmlspecialchars($dep['relationship']); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="/features/residents/admin/assets/css/residents.css">
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('familiesFilterForm');
    if (!form) return;
    
    const inputs = form.querySelectorAll('input');
    let timeout = null;

    function fetchResults() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const url = window.location.pathname + '?' + params.toString();
        
        const container = document.getElementById('familiesListContainer');
        if (container) container.style.opacity = '0.5';
        
        fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('familiesListContainer');
            
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
        input.addEventListener('input', () => {
            clearTimeout(timeout);
            timeout = setTimeout(fetchResults, 300);
        });
    });
});
</script>
