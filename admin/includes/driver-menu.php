<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a class="app-brand-link">
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
        <li class="menu-item <?= $currentPage === 'driver-dashboard.php' ? 'active' : '' ?>">
            <a href="driver-dashboard.php" class="menu-link">
                <i class="menu-icon bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- Schedule -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Schedule</span>
        </li>
        <li class="menu-item <?= $currentPage === 'driver-schedule.php' ? 'active' : '' ?>">
            <a href="driver-schedule.php" class="menu-link">
                <i class="menu-icon bx bx-calendar"></i>
                <div>My Schedule</div>
            </a>
        </li>
        

        <!-- Vehicle -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Vehicle</span>
        </li>
        <li class="menu-item <?= $currentPage === 'driver-vehicle.php' ? 'active' : '' ?>">
            <a href="driver-vehicle.php" class="menu-link">
                <i class="menu-icon bx bx-car"></i>
                <div>My Vehicle</div>
            </a>
        </li>
        <li class="menu-item <?= $currentPage === 'driver-report-issue.php' ? 'active' : '' ?>">
            <a href="driver-report-issue.php" class="menu-link">
                <i class="menu-icon bx bx-error"></i>
                <div>Report Issue</div>
            </a>
        </li>

        <!-- Account -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Account</span>
        </li>
        <li class="menu-item <?= $currentPage === 'driver-profile.php' ? 'active' : '' ?>">
            <a href="driver-profile.php" class="menu-link">
                <i class="menu-icon bx bx-user"></i>
                <div>My Profile</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="logout.php" class="menu-link">
                <i class="menu-icon bx bx-power-off"></i>
                <div>Logout</div>
            </a>
        </li>
    </ul>
</aside>