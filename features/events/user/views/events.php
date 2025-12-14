<?php if (!empty($eventsError)): ?>
    <div class="card page-card">
        <div class="notice error" style="margin-top: 1rem;">
            <?php echo htmlspecialchars($eventsError); ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($events)): ?>
    <div class="card page-card">
        <div class="card card--elevated" style="margin-top: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <h3>Upcoming Events</h3>
                <p>Click to view the detail.</p>
            </div>

            <!-- Filter Card (Financial Style) -->
            <div class="card card--filter" style="width: 100%; margin-bottom: 1.5rem;">
                <div class="filter-header" onclick="document.getElementById('userEventsFilterContent').style.display = document.getElementById('userEventsFilterContent').style.display === 'none' ? 'block' : 'none'" style="cursor: pointer;">
                    <div class="filter-icon"><i class="fas fa-filter"></i></div>
                    <h4 class="filter-title" style="flex: 1;">Filter Events</h4>
                    <i class="fas fa-chevron-down" style="color: #94a3b8;"></i>
                </div>
                
                <div id="userEventsFilterContent" style="padding: 1rem;">
                    <form id="userEventsFilterForm" method="get" style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #64748b; margin-bottom: 0.5rem;">Search</label>
                            <input type="text" name="search" placeholder="Search title, location..." value="<?php echo htmlspecialchars($search ?? ''); ?>" class="form-input" style="width: 100%;">
                        </div>
                    </form>
                </div>
            </div>
            
            <div id="eventsListContainer">
            <?php if (empty($events)): ?>
                <div class="empty-state" style="text-align: center; padding: 2rem; color: #666;">
                    <p>No events found matching your criteria.</p>
                </div>
            <?php else: ?>
            <div class="card-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem;">
                <?php foreach ($events as $e): ?>
                    <div class="card card--elevated event-card"
                         data-id="<?php echo (int)$e['id']; ?>"
                         data-title="<?php echo htmlspecialchars($e['title']); ?>"
                         data-description="<?php echo htmlspecialchars($e['description']); ?>"
                         data-image="<?php echo !empty($e['image_path']) ? url('/' . htmlspecialchars($e['image_path'])) : ''; ?>"
                         data-date="<?php echo htmlspecialchars($e['event_date'] ?? ''); ?>"
                         data-time="<?php echo htmlspecialchars($e['event_time'] ?? ''); ?>"
                         data-location="<?php echo htmlspecialchars($e['location'] ?? ''); ?>"
                         style="cursor:pointer;">
                        <?php if (!empty($e['image_path'])): ?>
                            <img src="<?php echo url('/' . htmlspecialchars($e['image_path'])); ?>" alt="<?php echo htmlspecialchars($e['title']); ?>" style="width:100%; height:auto; object-fit:contain; border-radius: 6px 6px 0 0;" />
                        <?php endif; ?>
                        <div class="card-body" style="padding: 1rem;">
                            <h4 style="margin: 0 0 .25rem;">
                                <?php echo htmlspecialchars($e['title']); ?>
                            </h4>
                            <p style="margin: 0 0 .5rem; color:#555;">
                                <?php echo nl2br(htmlspecialchars($e['description'])); ?>
                            </p>
                            <div style="font-size:.9rem; color:#666;">
                                <?php if (!empty($e['event_date'])): ?>
                                    <span><?php echo htmlspecialchars($e['event_date']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($e['event_time'])): ?>
                                    <span> • <?php echo htmlspecialchars($e['event_time']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($e['location'])): ?>
                                    <div><?php echo htmlspecialchars($e['location']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="eventModal" style="position:fixed; inset:0; background:rgba(0,0,0,.6); display:none; align-items:center; justify-content:center; padding:1rem; z-index:1000;">
        <div style="background:#fff; max-width:900px; width:100%; border-radius:8px; overflow:hidden; box-shadow:0 10px 20px rgba(0,0,0,.2);">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid #eee;">
                <h3 id="eventModalTitle" style="margin:0; font-size:1.25rem;"></h3>
                <button id="eventModalClose" aria-label="Close" style="border:none; background:transparent; font-size:1.25rem; cursor:pointer;">×</button>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0;">
                <div style="padding:1rem; border-right:1px solid #eee;">
                    <img id="eventModalImage" src="" alt="" style="width:100%; height:auto; object-fit:contain;" />
                </div>
                <div style="padding:1rem;">
                    <div id="eventModalDescription" style="color:#444; white-space:pre-wrap;"></div>
                    <div style="margin-top:1rem; font-size:.95rem; color:#666;">
                        <div id="eventModalMetaDate"></div>
                        <div id="eventModalMetaLocation" style="margin-top:.25rem;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Define globally or in a scope accessible to the filter script
    window.attachEventModalListeners = function() {};

    (function(){
        const modal = document.getElementById('eventModal');
        const mTitle = document.getElementById('eventModalTitle');
        const mImg = document.getElementById('eventModalImage');
        const mDesc = document.getElementById('eventModalDescription');
        const mDate = document.getElementById('eventModalMetaDate');
        const mLoc = document.getElementById('eventModalMetaLocation');
        const mClose = document.getElementById('eventModalClose');

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
            const dateStr = [data.date || '', data.time || ''].filter(Boolean).join(' • ');
            mDate.textContent = dateStr ? dateStr : '';
            mLoc.textContent = data.location ? ('Location: ' + data.location) : '';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(){
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        // Exported function to attach listeners
        window.attachEventModalListeners = function() {
            document.querySelectorAll('.event-card').forEach(function(card){
                // Remove old listeners to avoid duplicates if called multiple times? 
                // Cloning node is a cheap way to remove listeners, but let's just assume we are replacing the innerHTML so new elements are fresh.
                card.addEventListener('click', function(){
                    openModal({
                        title: card.getAttribute('data-title'),
                        description: card.getAttribute('data-description'),
                        image: card.getAttribute('data-image'),
                        date: card.getAttribute('data-date'),
                        time: card.getAttribute('data-time'),
                        location: card.getAttribute('data-location')
                    });
                });
            });
        };

        // Initial attachment
        window.attachEventModalListeners();

        mClose.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e){
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') closeModal();
        });
    })();
    
    // Dynamic Filter Script
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('userEventsFilterForm');
        if (!form) return;
        
        const inputs = form.querySelectorAll('input');
        let timeout = null;

        function fetchResults() {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const url = window.location.pathname + '?' + params.toString();
            
            const container = document.getElementById('eventsListContainer');
            if (container) container.style.opacity = '0.5';
            
            fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContainer = doc.getElementById('eventsListContainer');
                
                if (newContainer && container) {
                    container.innerHTML = newContainer.innerHTML;
                    // Re-attach modal listeners to new elements
                    if (window.attachEventModalListeners) {
                        window.attachEventModalListeners();
                    }
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
<?php else: ?>
    <!-- No placeholder when no events -->
<?php endif; ?>
