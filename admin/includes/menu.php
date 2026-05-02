<!-- Menu -->
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="index.php" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span style="color:rgb(69, 31, 204);">
                    <svg width="25" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="TSgradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#7367F0;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#A66FFE;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <rect fill="url(#TSgradient)" x="0" y="0" width="32" height="32" rx="6"></rect>
                        <text x="16" y="22" font-family="Arial, sans-serif" font-size="16" font-weight="bold" text-anchor="middle" fill="#FFFFFF">TS</text>
                    </svg>
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">UG TransPass</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left bx-sm"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item <?= $currentPage === 'index.php' ? 'active' : '' ?>">
      <a href="index.php" class="menu-link">
        <i class="menu-icon icon-base bx bx-home-circle"></i>
        <div data-i18n="Dashboard">Dashboard</div>
      </a>
    </li>

    <!-- Vehicle Management Section -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text"> Management</span>
    </li>
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base bx bx-car"></i>
        <div data-i18n="Vehicle">Vehicle Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= $currentPage === 'app-fleet-list.php' ? 'active' : '' ?>">
          <a href="app-fleet-list.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-list-ul"></i>
            <div data-i18n="List">Vehicle List</div>
          </a>
        </li>
        <li class="menu-item <?= $currentPage === 'app-fleet-add.php' ? 'active' : '' ?>">
          <a href="app-fleet-add.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-plus"></i>
            <div data-i18n="Add">Add Vehicle</div>
          </a>
        </li>
        <li class="menu-item <?= in_array($currentPage, ['app-maintenance.php','app-maintenance-view.php','app-maintenance-edit.php']) ? 'active' : '' ?>">
          <a href="app-maintenance.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-wrench"></i>
            <div data-i18n="Maintenance">Maintenance</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Driver Management Section -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base bx bx-user"></i>
        <div data-i18n="Drivers">Driver Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= $currentPage === 'app-driver-list.php' ? 'active' : '' ?>">
          <a href="app-driver-list.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-list-ul"></i>
            <div data-i18n="List">Driver List</div>
          </a>
        </li>
        <li class="menu-item <?= $currentPage === 'app-driver-add.php' ? 'active' : '' ?>">
          <a href="app-driver-add.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-user-plus"></i>
            <div data-i18n="Add">Add Driver</div>
          </a>
        </li>
        <li class="menu-item <?= in_array($currentPage, ['app-driver-schedule.php','driver-schedule.php']) ? 'active' : '' ?>">
          <a href="app-driver-schedule.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-calendar"></i>
            <div data-i18n="Schedule">Schedule</div>
          </a>
        </li>
       
      </ul>
    </li>

    <!-- Route Management Section -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base bx bx-map"></i>
        <div data-i18n="Routes">Route Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= $currentPage === 'app-route-list.php' ? 'active' : '' ?>">
          <a href="app-route-list.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-list-ul"></i>
            <div data-i18n="List">Route List</div>
          </a>
        </li>
        <li class="menu-item <?= $currentPage === 'app-route-add.php' ? 'active' : '' ?>">
          <a href="app-route-add.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-plus"></i>
            <div data-i18n="Add">Add Route</div>
          </a>
        </li>
        
        <li class="menu-item <?= in_array($currentPage, ['app-route-schedule.php','app-route-schedule-save.php','app-route-schedule-delete.php']) ? 'active' : '' ?>">
          <a href="app-route-schedule.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-time"></i>
            <div data-i18n="Schedule">Schedule</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Bookings Section -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base bx bx-book"></i>
        <div data-i18n="Bookings">Bookings Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= in_array($currentPage, ['app-booking-list.php','app-booking-view.php']) ? 'active' : '' ?>">
          <a href="app-booking-list.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-list-ul"></i>
            <div data-i18n="List">Booking List</div>
          </a>
        </li>
        
      </ul>
    </li>

    <!-- Reports Management Section -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base bx bx-file"></i>
        <div data-i18n="Reports">Reports Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= in_array($currentPage, ['app-reports-list.php','app-report-details.php']) ? 'active' : '' ?>">
          <a href="app-reports-list.php" class="menu-link">
            <i class="menu-icon icon-base bx bx-list-ul"></i>
            <div data-i18n="List">Reports List</div>
          </a>
        </li>
      </ul>
    </li>
</aside>
<!-- / Menu -->