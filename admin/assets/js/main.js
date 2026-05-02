// Main UI initializations for admin layout
(function () {
  function initMenu() {
    try {
      var menuEl = document.getElementById('layout-menu');
      if (menuEl && window.Menu && !menuEl.menuInstance) {
        // Initialize vertical menu instance
        window.Helpers.mainMenu = new window.Menu(menuEl, {
          orientation: 'vertical',
          animate: true,
          accordion: true,
          closeChildren: false,
          showDropdownOnHover: false
        });
      }
    } catch (e) {
      // Silently ignore to avoid breaking page if assets are missing
    }
  }

  function bindMenuTogglers() {
    var togglers = document.querySelectorAll('.layout-menu-toggle');
    if (!togglers.length || !window.Helpers) return;
    togglers.forEach(function (toggler) {
      if (toggler._menuToggleBound) return;
      toggler.addEventListener('click', function (e) {
        e.preventDefault();
        try {
          window.Helpers.toggleCollapsed(true);
        } catch (_) {}
      });
      toggler._menuToggleBound = true;
    });
  }

  function initThemeSwitcher() {
    var themeToggles = document.querySelectorAll('[data-bs-theme-value]');
    if (!themeToggles.length || !window.Helpers) return;
    themeToggles.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var theme = btn.getAttribute('data-bs-theme-value');
        window.Helpers.setTheme(theme);
        window.Helpers.showActiveTheme(theme, false);
      });
    });

    // Show active theme on load
    var currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
    window.Helpers.showActiveTheme(currentTheme, false);
  }

  function onReady(cb) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', cb, { once: true });
    } else {
      cb();
    }
  }

  onReady(function () {
    initMenu();
    bindMenuTogglers();
    initThemeSwitcher();
  });

  // In case of PJAX/partial reloads, expose re-init
  window.AdminMain = {
    initMenu: initMenu,
    bindMenuTogglers: bindMenuTogglers,
    initThemeSwitcher: initThemeSwitcher
  };
})();



