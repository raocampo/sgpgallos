(() => {
  const checkboxes = document.querySelectorAll('input[type=checkbox][name="medida"]');
  const inputPeso = document.querySelector('#peso');

  if (!checkboxes.length || !inputPeso) {
    return;
  }

  const syncPesoState = () => {
    inputPeso.disabled = document.querySelectorAll('input[type=checkbox][name="medida"]:checked').length === 0;
  };

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', () => {
      checkboxes.forEach((current) => {
        if (current !== checkbox) {
          current.checked = false;
        }
      });

      syncPesoState();
    });
  });

  syncPesoState();
})();
