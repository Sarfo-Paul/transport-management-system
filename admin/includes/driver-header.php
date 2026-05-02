<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>UG TransPass - <?= $page_title ?? 'Driver Portal' ?></title>
    
    <!-- Favicon -->
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' version='1.1'>
  <defs><linearGradient id='TSgradient' x1='0%' y1='0%' x2='100%' y2='100%'>
  <stop offset='0%' style='stop-color:%237367F0;stop-opacity:1'/>
<stop offset='100%' style='stop-color:%23A66FFE;stop-opacity:1'/></linearGradient>
</defs><rect fill='url(%23TSgradient)' x='0' y='0' width='32' height='32' rx='6'></rect><text x='16' y='22' font-family='Arial,sans-serif' font-size='16' font-weight='bold' text-anchor='middle' fill='%23FFFFFF'>TS</text></svg>" />    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css">
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css">
    <link rel="stylesheet" href="assets/css/demo.css">
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css">
    
    <!-- Page CSS -->
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="assets/css/<?= $page_css ?>">
    <?php endif; ?>
    
    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>
</head>