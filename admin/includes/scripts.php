    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js  -->

    <script src="assets/vendor/libs/jquery/jquery.js"></script>

    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/@algolia/autocomplete-js.js"></script>

    <script src="assets/vendor/libs/pickr/pickr.js"></script>

    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets/vendor/libs/hammer/hammer.js"></script>

    <script src="assets/vendor/libs/i18n/i18n.js"></script>

    <script src="assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <!-- Main JS -->

    <script src="assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="assets/js/app-logistics-dashboard.js"></script>

    <script>
      // Ensure menu is collapsed on initial load for all layouts (admin/user/driver)
      (function () {
        try {
          if (window.Helpers && !window.Helpers.isCollapsed()) {
            // Use the framework helper to apply the class with transition handling
            window.Helpers.setCollapsed(true, false);
          } else {
            // Fallback: ensure class is present early
            document.documentElement.classList.add('layout-menu-collapsed');
          }
        } catch (e) {
          document.documentElement.classList.add('layout-menu-collapsed');
        }
      })();
    </script>

    <script>
      // Lightweight input validators. Add classes to inputs to enforce constraints:
      // .only-numbers → digits only; .only-letters → letters and spaces; .only-alphanum → letters, digits, spaces; .plate-code → uppercase letters/digits/hyphen
      (function () {
        function bind(selector, regex) {
          document.addEventListener('input', function (e) {
            const t = e.target;
            if (!t.matches(selector)) return;
            const start = t.selectionStart;
            const end = t.selectionEnd;
            const cleaned = (t.value || '').replace(regex, '');
            if (cleaned !== t.value) {
              t.value = cleaned;
              // Restore caret position best-effort
              try { t.setSelectionRange(start - 1, end - 1); } catch (_) {}
            }
          });
        }

        // Remove any character NOT allowed
        bind('input.only-numbers', /[^0-9]/g);
        bind('input.only-letters', /[^a-zA-Z\s]/g);
        bind('input.only-alphanum', /[^a-zA-Z0-9\s]/g);
        bind('input.plate-code', /[^A-Z0-9-]/g);

        // Auto-uppercase for certain fields
        document.addEventListener('input', function (e) {
          if (e.target && e.target.matches('input.uppercase, input.plate-code')) {
            e.target.value = (e.target.value || '').toUpperCase();
          }
        });

        // Numeric min/max guard for number inputs
        document.addEventListener('change', function (e) {
          const t = e.target;
          if (t && t.type === 'number') {
            if (t.min !== '' && t.value !== '' && Number(t.value) < Number(t.min)) t.value = t.min;
            if (t.max !== '' && t.value !== '' && Number(t.value) > Number(t.max)) t.value = t.max;
          }
        });
      })();
    </script>