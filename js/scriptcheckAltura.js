(() => {
  const checkboxes = document.querySelectorAll('input[type=checkbox][name="medidaAltura"]');
  const inputAltura = document.querySelector('#altura');

  if (!checkboxes.length || !inputAltura) {
    return;
  }

  const syncAlturaState = () => {
    inputAltura.disabled = document.querySelectorAll('input[type=checkbox][name="medidaAltura"]:checked').length === 0;
  };

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', () => {
      checkboxes.forEach((current) => {
        if (current !== checkbox) {
          current.checked = false;
        }
      });

      syncAlturaState();
    });
  });

  syncAlturaState();
})();
