<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
       

        <ul class="navbar-nav flex-row align-items-center ms-auto">

              <!-- Style Switcher -->
      <li class="nav-item dropdown me-2 me-xl-0">
        <a
          class="nav-link dropdown-toggle hide-arrow"
          id="nav-theme"
          href="javascript:void(0);"
          data-bs-toggle="dropdown">
          <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
          <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
          <li>
            <button
              type="button"
              class="dropdown-item align-items-center active"
              data-bs-theme-value="light"
              aria-pressed="false">
              <span><i class="icon-base bx bx-sun icon-md me-3" data-icon="sun"></i>Light</span>
            </button>
          </li>
          <li>
            <button
              type="button"
              class="dropdown-item align-items-center"
              data-bs-theme-value="dark"
              aria-pressed="true">
              <span><i class="icon-base bx bx-moon icon-md me-3" data-icon="moon"></i>Dark</span>
            </button>
          </li>
          <li>
            <button
              type="button"
              class="dropdown-item align-items-center"
              data-bs-theme-value="system"
              aria-pressed="false">
              <span><i class="icon-base bx bx-desktop icon-md me-3" data-icon="desktop"></i>System</span>
            </button>
          </li>
        </ul>
      </li>
      <!-- / Style Switcher-->
         <!-- Notifications (dynamic) -->
<li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bx bx-bell bx-sm"></i>
    <span class="badge bg-danger rounded-pill badge-notifications" id="nav-notif-count" hidden>0</span>
  </a>
  <ul class="dropdown-menu dropdown-menu-end py-0 overflow-hidden" id="nav-notif-menu">
    <li class="dropdown-menu-header border-bottom">
      <div class="dropdown-header d-flex align-items-center py-3">
        <h5 class="text-body mb-0 me-auto">Notifications</h5>
        <span class="badge rounded-pill bg-label-primary" id="nav-notif-new">0 New</span>
      </div>
    </li>

    <!-- Make this scrollable -->
    <li class="dropdown-notifications-list" id="nav-notif-list" style="max-height: 300px; overflow-y: auto;">
      <ul class="list-group list-group-flush" id="nav-notif-items">
        <li class="list-group-item text-center text-muted small">No notifications</li>
      </ul>
    </li>


  </ul>
</li>


            <!-- Quick Actions -->
            <li class="nav-item dropdown me-3 me-xl-1">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="bx bx-grid-alt bx-sm"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-2">
                    <li class="mb-2"><a class="dropdown-item rounded" href="driver-report-issue.php"><i class="bx bx-error-circle me-2"></i><span>Report Issue</span></a></li>
                    <li><a class="dropdown-item rounded" href="driver-vehicle.php"><i class="bx bx-check-circle me-2"></i><span>Vehicle Check</span></a></li>
                </ul>
            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <span class="avatar-initial rounded-circle bg-label-success"><?= strtoupper(substr($_SESSION['first_name'] ?? 'D', 0, 1)) ?></span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="driver-profile.php">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-label-success"><?= strtoupper(substr($_SESSION['first_name'] ?? 'D', 0, 1)) ?></span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">Driver <?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?></span>
                                    <small class="text-muted">Licensed Driver</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li><a class="dropdown-item" href="driver-profile.php"><i class="bx bx-user me-2"></i><span class="align-middle">My Profile</span></a></li>
                    <li><div class="dropdown-divider"></div></li>
                    <li><a class="dropdown-item" href="logout.php"><i class="bx bx-power-off me-2"></i><span class="align-middle">Log Out</span></a></li>
                </ul>
            </li>
            <!-- /User -->
        </ul>
    </div>
</nav>
<script>
  // Driver navbar real-time notifications (graceful fallback if endpoint missing)
  (function(){
    const countEl = document.getElementById('nav-notif-count');
    const listEl = document.getElementById('nav-notif-items');
    const newEl = document.getElementById('nav-notif-new');
    async function fetchNotifs(){
      try{
        const res = await fetch('get-notifications.php?scope=driver', { credentials: 'same-origin' });
        if(!res.ok) throw new Error('Bad status');
        const data = await res.json();
        const items = Array.isArray(data.items) ? data.items : [];
        // Update badge
        if(items.length){
          countEl.textContent = items.length;
          countEl.hidden = false;
          newEl.textContent = items.length + ' New';
        } else {
          countEl.hidden = true;
          newEl.textContent = '0 New';
        }
        // Render list
        listEl.innerHTML = '';
        if(items.length === 0){
          const li = document.createElement('li');
          li.className = 'list-group-item text-center text-muted small';
          li.textContent = 'No notifications';
          listEl.appendChild(li);
        } else {
          items.slice(0,5).forEach(n => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action dropdown-notifications-item';
            li.innerHTML = '<div class="d-flex">\
              <div class="flex-shrink-0 me-3"><div class="avatar"><span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-bell"></i></span></div></div>\
              <div class="flex-grow-1"><h6 class="mb-1">'+(n.title||'Notification')+'</h6><p class="mb-0">'+(n.message||'')+'</p><small class="text-muted">'+(n.time||'Just now')+'</small></div>\
            </div>';
            listEl.appendChild(li);
          });
        }
      }catch(e){ /* silent */ }
    }
    fetchNotifs();
    setInterval(fetchNotifs, 30000);
  })();
</script>

