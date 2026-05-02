<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="icon-base bx bx-menu icon-md"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
    <ul class="navbar-nav flex-row align-items-center ms-md-auto">
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
      <li class="nav-item dropdown me-2 me-xl-2">
        <a class="nav-link dropdown-toggle hide-arrow position-relative" href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
          <i class="icon-base bx bx-bell icon-md"></i>
          <span class="position-absolute top-0 start-50 translate-middle-y badge rounded-pill bg-danger" id="admin-notif-count" hidden>0</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0 overflow-hidden" id="admin-notif-menu">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h5 class="text-body mb-0 me-auto">Notifications</h5>
              <span class="badge rounded-pill bg-label-primary" id="admin-notif-new">0 New</span>
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush" id="admin-notif-items">
              <li class="list-group-item text-center text-muted small">No notifications</li>
            </ul>
          </li>
         
        </ul>
      </li>

      <!-- Quick Actions -->
      <li class="nav-item dropdown me-2 me-xl-2">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="icon-base bx bx-plus-circle icon-md"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="app-driver-add.php"><i class="bx bx-user-plus me-2"></i><span class="align-middle">Add Driver</span></a></li>
          <li><a class="dropdown-item" href="app-fleet-add.php"><i class="bx bx-car me-2"></i><span class="align-middle">Add Vehicle</span></a></li>
          <li><a class="dropdown-item" href="app-route-add.php"><i class="bx bx-map me-2"></i><span class="align-middle">Add Route</span></a></li>
        </ul>
      </li>

      <!-- User Profile -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" id="nav-avatar">
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="profile.php">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" id="nav-avatar-menu">
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block" id="nav-user-name"><?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?></span>
                  <small class="text-muted" id="nav-user-role"><?= ucfirst(htmlspecialchars($_SESSION['role'] ?? 'User')) ?></small>
                </div>
              </div>
            </a>
          </li>
          <li><div class="dropdown-divider"></div></li>
          <li><a class="dropdown-item" href="profile.php"><i class="bx bx-user me-2"></i><span class="align-middle">My Profile</span></a></li>
          <li><div class="dropdown-divider"></div></li>
          <li><a class="dropdown-item" href="logout.php"><i class="bx bx-power-off me-2"></i><span class="align-middle">Log Out</span></a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav>

<style>
  /* Scrollable notifications */
  #admin-notif-menu .dropdown-notifications-list {
    max-height: 300px;   /* adjust height */
    overflow-y: auto;    /* enable vertical scroll */
  }
</style>

<script>
  // Admin navbar dynamic notifications and user info
  (function(){
    const countEl = document.getElementById('admin-notif-count');
    const itemsEl = document.getElementById('admin-notif-items');
    const newEl = document.getElementById('admin-notif-new');
    const nameEl = document.getElementById('nav-user-name');
    const roleEl = document.getElementById('nav-user-role');
    const avatarEls = [document.getElementById('nav-avatar'), document.getElementById('nav-avatar-menu')];

    async function fetchNav(){
      try{
        const res = await fetch('get-navbar-data.php', { credentials: 'same-origin' });
        if(!res.ok) throw new Error('Bad status');
        const data = await res.json();
        const notifs = Array.isArray(data.notifications) ? data.notifications : [];
        // Notifications
        if(notifs.length){
          countEl.hidden = false;
          countEl.textContent = notifs.length > 9 ? '9+' : notifs.length;
          newEl.textContent = (notifs.length) + ' New';
        } else {
          countEl.hidden = true;
          newEl.textContent = '0 New';
        }
        itemsEl.innerHTML = '';
        if(notifs.length === 0){
          const li = document.createElement('li');
          li.className = 'list-group-item text-center text-muted small';
          li.textContent = 'No notifications';
          itemsEl.appendChild(li);
        } else {
          notifs.slice(0,5).forEach(n => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action dropdown-notifications-item';
            li.innerHTML = '<div class="d-flex">\
              <div class="flex-shrink-0 me-3"><div class="avatar"><span class="avatar-initial rounded-circle bg-label-info"><i class="bx bx-bell"></i></span></div></div>\
              <div class="flex-grow-1"><h6 class="mb-1">'+(n.title||'Notification')+'</h6><p class="mb-0">'+(n.message||'')+'</p><small class="text-muted">'+(n.time||'Just now')+'</small></div>\
            </div>';
            itemsEl.appendChild(li);
          });
        }
        // User
        if(data.user){
          if(nameEl && data.user.name) nameEl.textContent = data.user.name;
          if(roleEl && data.user.role) roleEl.textContent = data.user.role;
          if(data.user.avatar){
            avatarEls.forEach(el => { if(el) el.src = data.user.avatar; });
          }
        }
      }catch(e){ /* silent */ }
    }
    fetchNav();
    setInterval(fetchNav, 30000);
  })();
</script>
