</main>
    </div>
</div>
<script src="<?php echo e(asset_url('js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset_url('js/scriptcheck.js')); ?>"></script>
<script src="<?php echo e(asset_url('js/scriptcheckAltura.js')); ?>"></script>
<script>
  jQuery(function ($) {
    if ($.fn.DataTable) {
      var tables = $('[data-datatable="true"]');

      if (!tables.length && $('#tabla_id').length) {
        tables = $('#tabla_id');
      }

      tables.each(function () {
        if ($.fn.dataTable.isDataTable(this)) {
          return;
        }

        $(this).DataTable({
          pageLength: 25,
          lengthMenu: [
            [25, 50, 75, 100],
            [25, 50, 75, 100]
          ],
          language: {
            url: '<?php echo e(admin_url('templates/idiomas/espaniol.json')); ?>'
          }
        });
      });
    }

    $(document).on('click', '[data-confirm]', function (event) {
      if (!window.confirm($(this).data('confirm'))) {
        event.preventDefault();
      }
    });
  });

  (function () {
    var body = document.body;
    var sidebar = document.getElementById('app-sidebar');
    var toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
    var closeButtons = document.querySelectorAll('[data-sidebar-close]');

    if (!sidebar || !toggleButtons.length) {
      return;
    }

    function isMobileViewport() {
      return window.innerWidth < 992;
    }

    function syncToggleState() {
      var expanded = isMobileViewport()
        ? body.classList.contains('sidebar-open')
        : !body.classList.contains('sidebar-collapsed');

      toggleButtons.forEach(function (button) {
        button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      });
    }

    function setSidebarState(open) {
      if (isMobileViewport()) {
        body.classList.toggle('sidebar-open', open);
      } else {
        body.classList.toggle('sidebar-collapsed', !open);
      }

      syncToggleState();
    }

    toggleButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        var open = isMobileViewport()
          ? !body.classList.contains('sidebar-open')
          : body.classList.contains('sidebar-collapsed');

        setSidebarState(open);
      });
    });

    closeButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        if (isMobileViewport()) {
          body.classList.remove('sidebar-open');
        } else {
          body.classList.add('sidebar-collapsed');
        }

        syncToggleState();
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        if (isMobileViewport()) {
          body.classList.remove('sidebar-open');
          syncToggleState();
        }
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth >= 992) {
        body.classList.remove('sidebar-open');
      } else {
        body.classList.remove('sidebar-collapsed');
      }

      syncToggleState();
    });

    syncToggleState();
  })();
</script>
</body>
</html>
