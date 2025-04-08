<!doctype html>
<html
  lang="en"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-skin="default"
  data-assets-path="assets/"
  data-template="vertical-menu-template"
  data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard - Transport Management System</title>

    <meta name="description" content="Advanced Transport Management System Dashboard" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/pickr/pickr-themes.css" />
    <link rel="stylesheet" href="assets/vendor/css/core.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="assets/vendor/libs/animate-css/animate.min.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="assets/vendor/css/pages/app-logistics-dashboard.css" />

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/vendor/js/template-customizer.js"></script>
    <script src="assets/js/config.js"></script>
    
    <style>
      /* Custom enhancements */
      .dashboard-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: none;
      }
      .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      }
      .status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 50px;
      }
      .quick-action-btn {
        transition: all 0.2s ease;
      }
      .quick-action-btn:hover {
        transform: translateX(5px);
      }
      .vehicle-img-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
      }
      .map-container {
        height: 100%;
        min-height: 200px;
        border-radius: 8px;
        overflow: hidden;
        background: #f5f5f5;
        position: relative;
      }
      .map-container .map-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 100;
      }
      .driver-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
      }
      .real-time-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
      }
      .real-time-active {
        background-color: #28C76F;
        animation: pulse 1.5s infinite;
      }
      @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(40, 199, 111, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0); }
      }
      .route-line {
        position: relative;
        padding-left: 20px;
      }
      .route-line:before {
        content: "";
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #7367F0;
      }
      .route-line:last-child:before {
        bottom: calc(100% - 20px);
      }
      .route-dot {
        position: absolute;
        left: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
      }
      .route-dot-primary {
        background-color: rgba(115, 103, 240, 0.1);
      }
      .route-dot-primary:after {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #7367F0;
      }
      .route-dot-success {
        background-color: rgba(40, 199, 111, 0.1);
      }
      .route-dot-success:after {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #28C76F;
      }
      .fleet-card {
        border-left: 4px solid;
      }
      .fleet-card-primary {
        border-left-color: #7367F0;
      }
      .fleet-card-warning {
        border-left-color: #FF9F43;
      }
      .fleet-card-danger {
        border-left-color: #EA5455;
      }
      .fleet-card-success {
        border-left-color: #28C76F;
      }
      .maintenance-progress {
        height: 8px;
        border-radius: 4px;
      }
      .driver-status {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
      }
      .driver-status-online {
        background-color: #28C76F;
      }
      .driver-status-offline {
        background-color: #EA5455;
      }
      .driver-status-onduty {
        background-color: #7367F0;
      }
    </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Navbar -->
        <nav
          class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
          id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
              <i class="icon-base bx bx-menu icon-md"></i>
            </a>
          </div>

          <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
            <!-- Search -->
            <div class="navbar-nav align-items-center">
              <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                  <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete">                                             
                  You are    welcome  to  Transpass</span>
                </a>
              </div>
            </div>

            <!-- /Search -->

            <ul class="navbar-nav flex-row align-items-center ms-md-auto">
           
            
              <!-- Language -->
              <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <i class="icon-base bx bx-globe icon-md"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="javascript:void(0);" data-language="en" data-text-direction="ltr">
                      <span>English</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="javascript:void(0);" data-language="fr" data-text-direction="ltr">
                      <span>French</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!--/ Language -->

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
                </ul>
              </li>
              <!-- / Style Switcher-->

              <!-- Notification -->
              <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                <a
                  class="nav-link dropdown-toggle hide-arrow"
                  href="javascript:void(0);"
                  data-bs-toggle="dropdown"
                  data-bs-auto-close="outside"
                  aria-expanded="false">
                  <span class="position-relative">
                    <i class="icon-base bx bx-bell icon-md"></i>
                    <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                  </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-0">
                  <li class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-3">
                      <h6 class="mb-0 me-auto">Notifications</h6>
                      <div class="d-flex align-items-center h6 mb-0">
                        <span class="badge bg-label-primary me-2">5 New</span>
                        <a
                          href="javascript:void(0)"
                          class="dropdown-notifications-all p-2"
                          data-bs-toggle="tooltip"
                          data-bs-placement="top"
                          title="Mark all as read"
                          ><i class="icon-base bx bx-envelope-open text-heading"></i
                        ></a>
                      </div>
                    </div>
                  </li>
                  <li class="dropdown-notifications-list scrollable-container">
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-car"></i></span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="small mb-0">Maintenance Due</h6>
                            <small class="mb-1 d-block text-body">Vehicle ID: VH-2023-1256 needs service</small>
                            <small class="text-body-secondary">10:04 AM</small>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <span class="avatar-initial rounded-circle bg-label-info"><i class="bx bx-user"></i></span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="small mb-0">Driver Assigned</h6>
                            <small class="mb-1 d-block text-body">Driver John Doe assigned to Route 25</small>
                            <small class="text-body-secondary">9:20 AM</small>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-check-circle"></i></span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="small mb-0">Maintenance Completed</h6>
                            <small class="mb-1 d-block text-body">Vehicle ID: VH-2023-1024 maintenance done</small>
                            <small class="text-body-secondary">Yesterday</small>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-error-circle"></i></span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="small mb-0">Delay Alert</h6>
                            <small class="mb-1 d-block text-body">Route 12 delayed by 15 minutes</small>
                            <small class="text-body-secondary">Yesterday</small>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-plus-circle"></i></span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="small mb-0">New Vehicle Added</h6>
                            <small class="mb-1 d-block text-body">New bus added to fleet (ID: VH-2023-1301)</small>
                            <small class="text-body-secondary">2 days ago</small>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </li>
                  <li class="border-top">
                    <div class="d-grid p-4">
                      <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
                        <small class="align-middle">View all notifications</small>
                      </a>
                    </div>
                  </li>
                </ul>
              </li>
              <!--/ Notification -->
              
              <!-- User -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a
                  class="nav-link dropdown-toggle hide-arrow p-0"
                  href="javascript:void(0);"
                  data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <img src="assets/img/avatars/1.png" alt class="rounded-circle" />
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="pages-account-settings-account.html">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="mb-0">Admin User</h6>
                          <small class="text-body-secondary">Transport Manager</small>
                        </div>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider my-1"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="pages-profile-user.html">
                      <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="pages-account-settings-account.html">
                      <i class="icon-base bx bx-cog icon-md me-3"></i><span>Settings</span>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider my-1"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="login.php" target="_blank">
                      <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!--/ User -->
            </ul>
          </div>
        </nav>
        <!-- / Navbar -->

        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu">
          <div class="app-brand demo">
            <a href="index.html" class="app-brand-link">
              <span class="app-brand-logo demo">
                <span class="text-primary">
                  <svg
                    width="25"
                    viewBox="0 0 25 42"
                    version="1.1"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                      <path
                        d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                        id="path-1"></path>
                      <path
                        d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                        id="path-3"></path>
                      <path
                        d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                        id="path-4"></path>
                      <path
                        d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                        id="path-5"></path>
                    </defs>
                    <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                        <g id="Icon" transform="translate(27.000000, 15.000000)">
                          <g id="Mask" transform="translate(0.000000, 8.000000)">
                            <mask id="mask-2" fill="white">
                              <use xlink:href="#path-1"></use>
                            </mask>
                            <use fill="currentColor" xlink:href="#path-1"></use>
                            <g id="Path-3" mask="url(#mask-2)">
                              <use fill="currentColor" xlink:href="#path-3"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                            </g>
                            <g id="Path-4" mask="url(#mask-2)">
                              <use fill="currentColor" xlink:href="#path-4"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                            </g>
                          </g>
                          <g
                            id="Triangle"
                            transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                            <use fill="currentColor" xlink:href="#path-5"></use>
                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                          </g>
                        </g>
                      </g>
                    </g>
                  </svg>
                </span>
              </span>
              <span class="app-brand-text demo menu-text fw-bold ms-2">TransPass Pro</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="icon-base bx bx-chevron-left"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item active">
              <a href="index.html" class="menu-link">
                <i class="menu-icon icon-base bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
              </a>
            </li>

            <!-- Logistics Section -->
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Transport Management</span>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base bx bx-car"></i>
                <div data-i18n="Vehicle">Fleet Management</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-fleet-list.html" class="menu-link">
                    <div data-i18n="List">Vehicle List</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-fleet-add.html" class="menu-link">
                    <div data-i18n="Add">Add Vehicle</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-maintenance.html" class="menu-link">
                    <div data-i18n="Maintenance">Maintenance</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-fuel-log.html" class="menu-link">
                    <div data-i18n="Fuel">Fuel Tracking</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base bx bx-user"></i>
                <div data-i18n="Drivers">Driver Management</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-driver-list.html" class="menu-link">
                    <div data-i18n="List">Driver List</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-driver-add.html" class="menu-link">
                    <div data-i18n="Add">Add Driver</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-driver-schedule.html" class="menu-link">
                    <div data-i18n="Schedule">Schedules</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-driver-performance.html" class="menu-link">
                    <div data-i18n="Performance">Performance</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base bx bx-map"></i>
                <div data-i18n="Routes">Route Management</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-route-list.html" class="menu-link">
                    <div data-i18n="List">Route List</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-route-add.html" class="menu-link">
                    <div data-i18n="Add">Add Route</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-route-map.html" class="menu-link">
                    <div data-i18n="Map">Route Map</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-route-schedule.html" class="menu-link">
                    <div data-i18n="Schedule">Schedule</div>
                  </a>
                </li>
              </ul>
           
            <!-- Administration Section -->
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Administration</span>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base bx bx-cog"></i>
                <div data-i18n="Settings">System Settings</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-settings-general.html" class="menu-link">
                    <div data-i18n="General">General</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-settings-notifications.html" class="menu-link">
                    <div data-i18n="Notifications">Notifications</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-settings-integrations.html" class="menu-link">
                    <div data-i18n="Integrations">Integrations</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="app-reports.html" class="menu-link">
                <i class="menu-icon icon-base bx bx-file"></i>
                <div data-i18n="Reports">Reports & Analytics</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="app-help-center.html" class="menu-link">
                <i class="menu-icon icon-base bx bx-help-circle"></i>
                <div data-i18n="Help">Help Center</div>
              </a>
            </li>
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">
                <!-- Dashboard Header -->
                <div class="col-12 mb-4">
                  <div class="card dashboard-card">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h4 class="card-title mb-1">Transport Management Dashboard</h4>
                          <p class="text-muted mb-0">Real-time overview of your transport operations</p>
                        </div>
                        <div class="d-flex">
                          <div class="input-group input-group-sm me-3" style="width: 200px;">
                            <span class="input-group-text bg-transparent"><i class="icon-base bx bx-calendar"></i></span>
                            <input type="text" class="form-control border-start-0" id="dashboardDateRange" placeholder="Select date range">
                          </div>
                          <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-filter-alt"></i> Filter
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                              <li><a class="dropdown-item" href="javascript:void(0);">Today</a></li>
                              <li><a class="dropdown-item" href="javascript:void(0);">This Week</a></li>
                              <li><a class="dropdown-item" href="javascript:void(0);">This Month</a></li>
                              <li><hr class="dropdown-divider"></li>
                              <li><a class="dropdown-item" href="javascript:void(0);">Custom Range</a></li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Fleet Overview Cards -->
                <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card dashboard-card fleet-card fleet-card-primary">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                          <h5 class="card-title mb-0">Total Fleet</h5>
                          <small class="text-muted">All vehicles in operation</small>
                        </div>
                        <div class="avatar">
                          <div class="avatar-initial bg-label-primary rounded">
                            <i class="icon-base bx bx-car"></i>
                          </div>
                        </div>
                      </div>
                      <div class="d-flex align-items-center">
                        <h3 class="mb-0">142</h3>
                        <span class="text-success ms-2 fw-medium">
                          <i class="icon-base bx bx-up-arrow-alt"></i> 8.4%
                        </span>
                      </div>
                      <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <small class="text-muted mt-2">Capacity utilization</small>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card dashboard-card fleet-card fleet-card-success">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                          <h5 class="card-title mb-0">Active Vehicles</h5>
                          <small class="text-muted">Currently in service</small>
                        </div>
                        <div class="avatar">
                          <div class="avatar-initial bg-label-success rounded">
                            <i class="icon-base bx bx-check-circle"></i>
                          </div>
                        </div>
                      </div>
                      <div class="d-flex align-items-center">
                        <h3 class="mb-0">118</h3>
                        <span class="text-success ms-2 fw-medium">
                          <i class="icon-base bx bx-up-arrow-alt"></i> 5.2%
                        </span>
                      </div>
                      <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 78%;" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <small class="text-muted mt-2">83% of total fleet</small>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card dashboard-card fleet-card fleet-card-warning">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                          <h5 class="card-title mb-0">In Maintenance</h5>
                          <small class="text-muted">Undergoing service</small>
                        </div>
                        <div class="avatar">
                          <div class="avatar-initial bg-label-warning rounded">
                            <i class="icon-base bx bx-wrench"></i>
                          </div>
                        </div>
                      </div>
                      <div class="d-flex align-items-center">
                        <h3 class="mb-0">18</h3>
                        <span class="text-danger ms-2 fw-medium">
                          <i class="icon-base bx bx-down-arrow-alt"></i> 2.1%
                        </span>
                      </div>
                      <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 12%;" aria-valuenow="12" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <small class="text-muted mt-2">12% of total fleet</small>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card dashboard-card fleet-card fleet-card-danger">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                          <h5 class="card-title mb-0">Out of Service</h5>
                          <small class="text-muted">Not operational</small>
                        </div>
                        <div class="avatar">
                          <div class="avatar-initial bg-label-danger rounded">
                            <i class="icon-base bx bx-error-circle"></i>
                          </div>
                        </div>
                      </div>
                      <div class="d-flex align-items-center">
                        <h3 class="mb-0">6</h3>
                        <span class="text-success ms-2 fw-medium">
                          <i class="icon-base bx bx-down-arrow-alt"></i> 1.3%
                        </span>
                      </div>
                      <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 5%;" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <small class="text-muted mt-2">5% of total fleet</small>
                    </div>
                  </div>
                </div>

                <!-- Fleet Status Chart -->
                <div class="col-lg-8 mb-4">
                  <div class="card dashboard-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0">Fleet Status Overview</h5>
                      <div class="dropdown">
                        <button class="btn p-0" type="button" id="fleetStatusDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-base bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="fleetStatusDropdown">
                          <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                          <a class="dropdown-item" href="javascript:void(0);">Export</a>
                          <a class="dropdown-item" href="javascript:void(0);">Details</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div id="fleetStatusChart"></div>
                    </div>
                  </div>
                </div>

                <!-- Driver Status -->
                <div class="col-lg-4 mb-4">
                  <div class="card dashboard-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0">Driver Status</h5>
                      <div class="dropdown">
                        <button class="btn p-0" type="button" id="driverStatusDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-base bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="driverStatusDropdown">
                          <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                          <a class="dropdown-item" href="javascript:void(0);">Export</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-4">
                        <div class="avatar">
                          <div class="avatar-initial bg-label-primary rounded">
                            <i class="icon-base bx bx-user"></i>
                          </div>
                        </div>
                        <div class="ms-3">
                          <div class="d-flex align-items-center">
                            <h3 class="mb-0">92</h3>
                            <span class="text-success ms-2 fw-medium">
                              <i class="icon-base bx bx-up-arrow-alt"></i> 6.7%
                            </span>
                          </div>
                          <small class="text-muted">Active Drivers</small>
                        </div>
                      </div>
                      <div class="row text-center mb-4">
                        <div class="col-4 border-end">
                          <div class="h5 mb-0">65</div>
                          <small class="text-muted">On Duty</small>
                        </div>
                        <div class="col-4 border-end">
                          <div class="h5 mb-0">18</div>
                          <small class="text-muted">Available</small>
                        </div>
                        <div class="col-4">
                          <div class="h5 mb-0">9</div>
                          <small class="text-muted">On Leave</small>
                        </div>
                      </div>
                      <div id="driverStatusChart" class="mb-4"></div>
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          <div class="avatar avatar-sm me-2">
                            <img src="assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
                          </div>
                          <div class="avatar avatar-sm me-2">
                            <img src="assets/img/avatars/2.png" alt="Avatar" class="rounded-circle">
                          </div>
                          <div class="avatar avatar-sm me-2">
                            <img src="assets/img/avatars/3.png" alt="Avatar" class="rounded-circle">
                          </div>
                          <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-circle bg-label-primary">+12</span>
                          </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary">View All</button>
                      </div>
                    </div>
                  </div>
                </div>

            
                <!-- Active Routes -->
                <div class="col-lg-6 mb-4">
                  <div class="card dashboard-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0">Active Routes</h5>
                      <div class="dropdown">
                        <button class="btn p-0" type="button" id="activeRoutesDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-base bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="activeRoutesDropdown">
                          <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                          <a class="dropdown-item" href="javascript:void(0);">Export</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="route-line mb-3">
                        <div class="route-dot route-dot-primary">
                          <i class="bx bx-car text-primary"></i>
                        </div>
                        <div class="ps-4">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Route 12 - Downtown Express</h6>
                            <span class="badge bg-label-primary">On Time</span>
                          </div>
                          <p class="mb-1 small text-muted">
                            <span class="driver-status driver-status-onduty"></span>
                            Driver: John Doe • Vehicle: VH-2023-1024 (45 seats)
                          </p>
                          <p class="mb-0 small text-muted">
                            <i class="icon-base bx bx-time-five me-1"></i> Departed: 07:00 AM • ETA: 08:15 AM
                          </p>
                        </div>
                      </div>
                      <div class="route-line mb-3">
                        <div class="route-dot route-dot-success">
                          <i class="bx bx-bus text-success"></i>
                        </div>
                        <div class="ps-4">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Route 25 - Northside Loop</h6>
                            <span class="badge bg-label-success">Early</span>
                          </div>
                          <p class="mb-1 small text-muted">
                            <span class="driver-status driver-status-onduty"></span>
                            Driver: Jane Smith • Vehicle: VH-2023-1120 (30 seats)
                          </p>
                          <p class="mb-0 small text-muted">
                            <i class="icon-base bx bx-time-five me-1"></i> Departed: 06:45 AM • ETA: 08:00 AM
                          </p>
                        </div>
                      </div>
                      <div class="route-line mb-3">
                        <div class="route-dot route-dot-primary">
                          <i class="bx bx-car text-primary"></i>
                        </div>
                        <div class="ps-4">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Route 8 - Airport Shuttle</h6>
                            <span class="badge bg-label-warning">Delayed</span>
                          </div>
                          <p class="mb-1 small text-muted">
                            <span class="driver-status driver-status-onduty"></span>
                            Driver: Mike Johnson • Vehicle: VH-2023-1045 (35 seats)
                          </p>
                          <p class="mb-0 small text-muted">
                            <i class="icon-base bx bx-time-five me-1"></i> Departed: 08:15 AM • ETA: 09:45 AM (+15 min)
                          </p>
                        </div>
                      </div>
                      <div class="route-line">
                        <div class="route-dot route-dot-success">
                          <i class="bx bx-bus text-success"></i>
                        </div>
                        <div class="ps-4">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Route 17 - Southside Connector</h6>
                            <span class="badge bg-label-success">On Time</span>
                          </div>
                          <p class="mb-1 small text-muted">
                            <span class="driver-status driver-status-onduty"></span>
                            Driver: Sarah Williams • Vehicle: VH-2023-1089 (50 seats)
                          </p>
                          <p class="mb-0 small text-muted">
                            <i class="icon-base bx bx-time-five me-1"></i> Departed: 07:30 AM • ETA: 09:00 AM
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer text-center">
                      <a href="app-route-list.html" class="btn btn-sm btn-outline-primary">View All Routes</a>
                    </div>
                  </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card dashboard-card h-100">
                    <div class="card-header">
                      <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                      <div class="d-grid gap-3">
                        <button class="btn btn-primary d-flex align-items-center justify-content-between quick-action-btn">
                          <span>
                            <i class="icon-base bx bx-plus me-2"></i> Assign Route
                          </span>
                          <i class="icon-base bx bx-chevron-right"></i>
                        </button>
                        <button class="btn btn-outline-primary d-flex align-items-center justify-content-between quick-action-btn">
                          <span>
                            <i class="icon-base bx bx-user-plus me-2"></i> Add Driver
                          </span>
                          <i class="icon-base bx bx-chevron-right"></i>
                        </button>
                        <button class="btn btn-outline-primary d-flex align-items-center justify-content-between quick-action-btn">
                          <span>
                            <i class="icon-base bx bx-car me-2"></i> Add Vehicle
                          </span>
                          <i class="icon-base bx bx-chevron-right"></i>
                        </button>
                        <button class="btn btn-outline-primary d-flex align-items-center justify-content-between quick-action-btn">
                          <span>
                            <i class="icon-base bx bx-map me-2"></i> Create Route
                          </span>
                          <i class="icon-base bx bx-chevron-right"></i>
                        </button>
                        <button class="btn btn-outline-primary d-flex align-items-center justify-content-between quick-action-btn">
                          <span>
                            <i class="icon-base bx bx-calendar me-2"></i> Schedule Maintenance
                          </span>
                          <i class="icon-base bx bx-chevron-right"></i>
                        </button>
                        <button class="btn btn-outline-primary d-flex align-items-center justify-content-between quick-action-btn">
                          <span>
                            <i class="icon-base bx bx-file me-2"></i> Generate Report
                          </span>
                          <i class="icon-base bx bx-chevron-right"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Recent Maintenance -->
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card dashboard-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0">Recent Maintenance</h5>
                      <div class="dropdown">
                        <button class="btn p-0" type="button" id="maintenanceDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-base bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="maintenanceDropdown">
                          <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                          <a class="dropdown-item" href="javascript:void(0);">View All</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="list-group list-group-flush">
                        <div class="list-group-item list-group-item-action border-0 px-0 py-2">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">VH-2023-1120</h6>
                            <span class="badge bg-label-success">Completed</span>
                          </div>
                          <p class="mb-1 small text-muted">Oil change, brake inspection</p>
                          <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="icon-base bx bx-calendar me-1"></i> 2 days ago</small>
                            <div class="maintenance-progress bg-success" style="width: 100%;"></div>
                          </div>
                        </div>
                        <div class="list-group-item list-group-item-action border-0 px-0 py-2">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">VH-2023-1045</h6>
                            <span class="badge bg-label-warning">In Progress</span>
                          </div>
                          <p class="mb-1 small text-muted">Engine diagnostics, transmission check</p>
                          <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="icon-base bx bx-calendar me-1"></i> Today</small>
                            <div class="maintenance-progress bg-warning" style="width: 65%;"></div>
                          </div>
                        </div>
                        <div class="list-group-item list-group-item-action border-0 px-0 py-2">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">VH-2023-1089</h6>
                            <span class="badge bg-label-info">Scheduled</span>
                          </div>
                          <p class="mb-1 small text-muted">Tire rotation, alignment check</p>
                          <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="icon-base bx bx-calendar me-1"></i> Tomorrow</small>
                            <div class="maintenance-progress bg-info" style="width: 0%;"></div>
                          </div>
                        </div>
                        <div class="list-group-item list-group-item-action border-0 px-0 py-2">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">VH-2023-1024</h6>
                            <span class="badge bg-label-danger">Overdue</span>
                          </div>
                          <p class="mb-1 small text-muted">Annual inspection, safety check</p>
                          <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="icon-base bx bx-calendar me-1"></i> 5 days overdue</small>
                            <div class="maintenance-progress bg-danger" style="width: 0%;"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer text-center">
                      <a href="app-maintenance.html" class="btn btn-sm btn-outline-primary">View All Maintenance</a>
                    </div>
                  </div>
                </div>

                <!-- Fleet Management Table -->
                <div class="col-lg-8 col-md-12 mb-4">
                  <div class="card dashboard-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0">Fleet Management</h5>
                      <div class="d-flex">
                        <div class="input-group input-group-sm me-2" style="width: 200px;">
                          <span class="input-group-text bg-transparent"><i class="icon-base bx bx-search"></i></span>
                          <input type="text" class="form-control border-start-0" placeholder="Search vehicles...">
                        </div>
                        <button class="btn btn-sm btn-primary">
                          <i class="icon-base bx bx-plus me-1"></i> Add Vehicle
                        </button>
                      </div>
                    </div>
                    <div class="card-datatable table-responsive">
                      <table class="datatables-vehicles table border-top">
                        <thead>
                          <tr>
                            <th></th>
                            <th>Vehicle ID</th>
                            <th>Type</th>
                            <th>Registration</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Current Route</th>
                            <th>Driver</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <img src="assets/img/vehicles/t1.png" class="vehicle-img-thumb" alt="Vehicle">
                            </td>
                            <td>VH-2023-1001</td>
                            <td>Bus</td>
                            <td>GT-1234-21</td>
                            <td>45</td>
                            <td><span class="badge bg-label-success">Active</span></td>
                            <td>Route 12</td>
                            <td>
                              <div class="d-flex align-items-center">
                                <img src="assets/img/avatars/1.png" class="driver-avatar me-2" alt="Driver">
                                <span>John Doe</span>
                              </div>
                            </td>
                            <td>
                              <div class="d-inline-block">
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Edit"><i class="icon-base bx bx-edit"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Track"><i class="icon-base bx bx-map"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Maintenance"><i class="icon-base bx bx-wrench"></i></button>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <img src="assets/img/vehicles/t1.png" class="vehicle-img-thumb" alt="Vehicle">
                            </td>
                            <td>VH-2023-1002</td>
                            <td>Minibus</td>
                            <td>GT-5678-21</td>
                            <td>30</td>
                            <td><span class="badge bg-label-warning">Maintenance</span></td>
                            <td>-</td>
                            <td>-</td>
                            <td>
                              <div class="d-inline-block">
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Edit"><i class="icon-base bx bx-edit"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Track"><i class="icon-base bx bx-map"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Maintenance"><i class="icon-base bx bx-wrench"></i></button>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <img src="assets/img/vehicles/t1.png" class="vehicle-img-thumb" alt="Vehicle">
                            </td>
                            <td>VH-2023-1003</td>
                            <td>Bus</td>
                            <td>GT-9012-21</td>
                            <td>50</td>
                            <td><span class="badge bg-label-danger">Out of Service</span></td>
                            <td>-</td>
                            <td>-</td>
                            <td>
                              <div class="d-inline-block">
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Edit"><i class="icon-base bx bx-edit"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Track"><i class="icon-base bx bx-map"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Maintenance"><i class="icon-base bx bx-wrench"></i></button>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <img src="assets/img/vehicles/t1.png" class="vehicle-img-thumb" alt="Vehicle">
                            </td>
                            <td>VH-2023-1004</td>
                            <td>Minibus</td>
                            <td>GT-3456-21</td>
                            <td>35</td>
                            <td><span class="badge bg-label-success">Active</span></td>
                            <td>Route 8</td>
                            <td>
                              <div class="d-flex align-items-center">
                                <img src="assets/img/avatars/3.png" class="driver-avatar me-2" alt="Driver">
                                <span>Mike Johnson</span>
                              </div>
                            </td>
                            <td>
                              <div class="d-inline-block">
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Edit"><i class="icon-base bx bx-edit"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Track"><i class="icon-base bx bx-map"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Maintenance"><i class="icon-base bx bx-wrench"></i></button>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <img src="assets/img/vehicles/t1.png" class="vehicle-img-thumb" alt="Vehicle">
                            </td>
                            <td>VH-2023-1005</td>
                            <td>Bus</td>
                            <td>GT-7890-21</td>
                            <td>50</td>
                            <td><span class="badge bg-label-success">Active</span></td>
                            <td>Route 17</td>
                            <td>
                              <div class="d-flex align-items-center">
                                <img src="assets/img/avatars/4.png" class="driver-avatar me-2" alt="Driver">
                                <span>Sarah Williams</span>
                              </div>
                            </td>
                            <td>
                              <div class="d-inline-block">
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Edit"><i class="icon-base bx bx-edit"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Track"><i class="icon-base bx bx-map"></i></button>
                                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Maintenance"><i class="icon-base bx bx-wrench"></i></button>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  © <script>document.write(new Date().getFullYear())</script> <strong>TransPass Pro</strong> - Transport Management System
                </div>
                <div>
                  <a href="javascript:void(0);" class="footer-link me-4">Help Center</a>
                  <a href="javascript:void(0);" class="footer-link me-4">Contact Support</a>
                  <a href="javascript:void(0);" class="footer-link">Privacy Policy</a>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/libs/hammer/hammer.js"></script>
    <script src="assets/vendor/libs/i18n/i18n.js"></script>
    <script src="assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
    <script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
    <script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
    <script src="assets/vendor/libs/moment/moment.js"></script>
    <script src="assets/vendor/libs/daterangepicker/daterangepicker.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="assets/js/dashboards-logistics.js"></script>

    <!-- Initialize DataTables -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize date range picker
      $('#dashboardDateRange').daterangepicker({
        opens: 'left',
        locale: {
          format: 'MMM D, YYYY'
        }
      });

      // Initialize tooltips
      $('[data-bs-toggle="tooltip"]').tooltip();
      
      // Vehicle Management Table
      $('.datatables-vehicles').DataTable({
        responsive: true,
        dom: '<"top"<"head-actions"f><"head-actions-right"l>>rt<"bottom"ip><"clear">',
        language: {
          search: '',
          searchPlaceholder: 'Search vehicles...'
        },
        columnDefs: [
          { orderable: false, targets: [0, 8] },
          { responsivePriority: 1, targets: 1 },
          { responsivePriority: 2, targets: 5 },
          { responsivePriority: 3, targets: 6 }
        ],
        initComplete: function() {
          $('.dataTables_filter input').addClass('form-control form-control-sm');
        }
      });

      // Fleet Status Chart
      var fleetStatusChart = new ApexCharts(document.querySelector("#fleetStatusChart"), {
        series: [{
          name: 'Active',
          data: [31, 40, 28, 51, 42, 109, 118]
        }, {
          name: 'Maintenance',
          data: [11, 32, 45, 32, 34, 22, 18]
        }, {
          name: 'Out of Service',
          data: [8, 5, 3, 7, 4, 6, 6]
        }],
        chart: {
          type: 'area',
          height: 350,
          stacked: true,
          toolbar: {
            show: false
          },
          zoom: {
            enabled: false
          }
        },
        colors: ['#28C76F', '#FF9F43', '#EA5455'],
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth',
          width: 2
        },
        fill: {
          type: 'gradient',
          gradient: {
            opacityFrom: 0.6,
            opacityTo: 0.8,
          }
        },
        legend: {
          position: 'top',
          horizontalAlign: 'left'
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val + " vehicles"
            }
          }
        }
      });
      fleetStatusChart.render();

      // Driver Status Chart
      var driverStatusChart = new ApexCharts(document.querySelector("#driverStatusChart"), {
        series: [65, 18, 9],
        chart: {
          height: 150,
          type: 'radialBar',
        },
        plotOptions: {
          radialBar: {
            dataLabels: {
              name: {
                fontSize: '14px',
              },
              value: {
                fontSize: '16px',
                fontWeight: '600',
              },
              total: {
                show: true,
                label: 'Total',
                formatter: function (w) {
                  return '92'
                }
              }
            }
          }
        },
        labels: ['On Duty', 'Available', 'On Leave'],
        colors: ['#7367F0', '#28C76F', '#FF9F43']
      });
      driverStatusChart.render();
    });
    </script>
  </body>
</html>