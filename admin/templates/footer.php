</main>
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
</script>
</body>
</html>
