<?php if (!empty($donationsError)): ?>
    <div class="card page-card">
        <div class="notice error" style="margin-top: 1rem;">
            <?php echo htmlspecialchars($donationsError); ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($donations)): ?>
    <div class="card card--elevated" style="margin-top: 2rem;">
        <div style="margin-bottom: 1.5rem;">
            <h3>Donations</h3>
            <p>Click to view the detail.</p>
        </div>

            <!-- Filter Card (Financial Style) -->
            <div class="card card--filter" style="width: 100%; margin-bottom: 1.5rem;">
                <div class="filter-header" onclick="document.getElementById('userDonationsFilterContent').style.display = document.getElementById('userDonationsFilterContent').style.display === 'none' ? 'block' : 'none'" style="cursor: pointer;">
                    <div class="filter-icon"><i class="fas fa-filter"></i></div>
                    <h4 class="filter-title" style="flex: 1;">Filter Donations</h4>
                    <i class="fas fa-chevron-down" style="color: #94a3b8;"></i>
                </div>
                
                <div id="userDonationsFilterContent" style="padding: 1rem;">
                    <form id="userDonationsFilterForm" method="get" style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #64748b; margin-bottom: 0.5rem;">Search</label>
                            <input type="text" name="search" placeholder="Search title, description..." value="<?php echo htmlspecialchars($search ?? ''); ?>" class="form-input" style="width: 100%;">
                        </div>
                    </form>
                </div>
            </div>
            
            <div id="donationsListContainer">
            <?php if (empty($donations)): ?>
                <div class="empty-state" style="text-align: center; padding: 2rem; color: #666;">
                    <p>No donations found matching your criteria.</p>
                </div>
            <?php else: ?>
            <div class="card-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem;">
                <?php foreach ($donations as $d): ?>
                    <div class="card card--elevated donation-card"
                         data-id="<?php echo (int)$d['id']; ?>"
                         data-title="<?php echo htmlspecialchars($d['title']); ?>"
                         data-description="<?php echo htmlspecialchars($d['description']); ?>"
                         data-image="<?php echo !empty($d['image_path']) ? url('/' . htmlspecialchars($d['image_path'])) : ''; ?>"
                         style="cursor:pointer;">
                        <?php if (!empty($d['image_path'])): ?>
                            <img src="<?php echo url('/' . htmlspecialchars($d['image_path'])); ?>" alt="<?php echo htmlspecialchars($d['title']); ?>" style="width:100%; height:auto; object-fit:contain; border-radius: 6px 6px 0 0;" />
                        <?php endif; ?>
                        <div class="card-body" style="padding: 1rem;">
                            <h4 style="margin: 0 0 .5rem;">
                                <?php echo htmlspecialchars($d['title']); ?>
                            </h4>
                            <p style="color:#555;">
                                <?php echo nl2br(htmlspecialchars($d['description'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            </div>
    </div>

    <!-- Modal -->
    <div id="donationModal" style="position:fixed; inset:0; background:rgba(0,0,0,.6); display:none; align-items:center; justify-content:center; padding:1rem; z-index:1000;">
        <div style="background:#fff; max-width:900px; width:100%; border-radius:8px; overflow:hidden; box-shadow:0 10px 20px rgba(0,0,0,.2);">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid #eee;">
                <h3 id="donationModalTitle" style="margin:0; font-size:1.25rem;"></h3>
                <button id="donationModalClose" aria-label="Close" style="border:none; background:transparent; font-size:1.25rem; cursor:pointer;">×</button>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0;">
                <div style="padding:1rem; border-right:1px solid #eee;">
                    <img id="donationModalImage" src="" alt="" style="width:100%; height:auto; object-fit:contain;" />
                </div>
                <div style="padding:1rem;">
                    <div id="donationModalDescription" style="color:#444; white-space:pre-wrap;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function(){
        const modal = document.getElementById('donationModal');
        const mTitle = document.getElementById('donationModalTitle');
        const mImg = document.getElementById('donationModalImage');
        const mDesc = document.getElementById('donationModalDescription');
        const mClose = document.getElementById('donationModalClose');

        function openModal(data){
            mTitle.textContent = data.title || '';
            if (data.image) {
                mImg.src = data.image;
                mImg.style.display = '';
            } else {
                mImg.removeAttribute('src');
                mImg.style.display = 'none';
            }
            mDesc.textContent = data.description || '';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(){
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        // Use event delegation for dynamic content
        document.addEventListener('click', function(e) {
            const card = e.target.closest('.donation-card');
            if (card) {
                openModal({
                    title: card.getAttribute('data-title'),
                    description: card.getAttribute('data-description'),
                    image: card.getAttribute('data-image')
                });
            }
        });

        mClose.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e){
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') closeModal();
        });

        // Dynamic Filter Script
        const form = document.getElementById('userDonationsFilterForm');
        if (form) {
            const inputs = form.querySelectorAll('input');
            let timeout = null;

            function fetchResults() {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                const url = window.location.pathname + '?' + params.toString();
                
                const container = document.getElementById('donationsListContainer');
                if (container) container.style.opacity = '0.5';
                
                fetch(url)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('donationsListContainer');
                    
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
        }
    })();
    </script>
<?php else: ?>
    <!-- No placeholder shown when there are no donations -->
<?php endif; ?>
