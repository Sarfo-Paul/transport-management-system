<?php
// Ensure backend context and fetch user data
if (!isset($conn)) { @include_once __DIR__ . '/../config.php'; }
$userId = $_SESSION['user_id'] ?? null;
$userInfo = null;
if (isset($conn) && $userId) {
	$userInfo = getUserData($userId);
}
$firstName = $userInfo['first_name'] ?? ($_SESSION['first_name'] ?? 'User');
$lastName = $userInfo['last_name'] ?? '';
$displayName = trim($firstName . ' ' . $lastName);
$initial = strtoupper(substr($firstName, 0, 1));
?>
<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <!-- Menu toggle -->
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="icon-base bx bx-menu icon-md"></i>
    </a>
  </div>

  <!-- Navbar Right -->
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

      <!-- Notifications -->
      <li class="nav-item dropdown me-2 me-xl-2">
        <a class="nav-link dropdown-toggle hide-arrow position-relative" href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
          <i class="icon-base bx bx-bell icon-md"></i>
          <span class="position-absolute top-0 start-50 translate-middle-y badge rounded-pill bg-danger" id="user-notif-count" hidden>0</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0 overflow-hidden" id="user-notif-menu">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h5 class="text-body mb-0 me-auto">Notifications</h5>
              <span class="badge rounded-pill bg-label-primary" id="user-notif-new">0 New</span>
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush" id="user-notif-items">
              <li class="list-group-item text-center text-muted small">No notifications</li>
            </ul>
          </li>
        </ul>
      </li>
      <!-- / Notifications -->



      <!-- User Profile -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <span class="avatar-initial rounded-circle bg-label-success" id="user-nav-avatar">
              <?= $initial ?>
            </span>
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="user-profile.php">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <span class="avatar-initial rounded-circle bg-label-success">
                      <?= $initial ?>
                    </span>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block"><?= htmlspecialchars($displayName) ?></span>
                  <small class="text-muted">User</small>
                </div>
              </div>
            </a>
          </li>
          <li><div class="dropdown-divider"></div></li>
          <li><a class="dropdown-item" href="user-profile.php"><i class="bx bx-user me-2"></i><span class="align-middle">My Profile</span></a></li>
          <li><div class="dropdown-divider"></div></li>
          <li><a class="dropdown-item" href="logout.php"><i class="bx bx-power-off me-2"></i><span class="align-middle">Log Out</span></a></li>
        </ul>
      </li>
      <!-- / User Profile -->

    </ul>
  </div>
</nav>

<style>
  /* Scrollable notifications */
  #user-notif-menu .dropdown-notifications-list {
    max-height: 300px;
    overflow-y: auto;
  }
</style>

<script>
  // User navbar dynamic notifications
  (function(){
    const countEl = document.getElementById('user-notif-count');
    const itemsEl = document.getElementById('user-notif-items');
    const newEl = document.getElementById('user-notif-new');

    async function fetchNotifs(){
      try{
        const res = await fetch('get-notifications.php?scope=user', { credentials: 'same-origin' });
        if(!res.ok) throw new Error('Bad status');
        const data = await res.json();
        const items = Array.isArray(data.items) ? data.items : [];

        // Badge update
        if(items.length){
          countEl.hidden = false;
          countEl.textContent = items.length > 9 ? '9+' : items.length;
          newEl.textContent = items.length + ' New';
        } else {
          countEl.hidden = true;
          newEl.textContent = '0 New';
        }

        // Render list
        itemsEl.innerHTML = '';
        if(items.length === 0){
          const li = document.createElement('li');
          li.className = 'list-group-item text-center text-muted small';
          li.textContent = 'No notifications';
          itemsEl.appendChild(li);
        } else {
          items.slice(0,5).forEach(n => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action dropdown-notifications-item';
            li.innerHTML = '<div class="d-flex">\
              <div class="flex-shrink-0 me-3"><div class="avatar"><span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-bell"></i></span></div></div>\
              <div class="flex-grow-1"><h6 class="mb-1>'+(n.title||'Notification')+'</h6><p class="mb-0>'+(n.message||'')+'</p><small class="text-muted>'+(n.time||'Just now')+'</small></div>\
            </div>';
            itemsEl.appendChild(li);
          });
        }
      }catch(e){ /* silent fail */ }
    }

    fetchNotifs();
    setInterval(fetchNotifs, 30000);
  })();
</script>
