(() => {
  const checkboxes = document.querySelectorAll('input[type=checkbox][name="medidaAltura"]');
  const inputAltura = document.querySelector('#altura');

  if (checkboxes.length && inputAltura) {
    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        if (document.querySelectorAll('input[type=checkbox][name="medidaAltura"]:checked').length > 0) {
          inputAltura.disabled = false;
        } else {
          inputAltura.disabled = true;
        }
      });
    });

    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        checkboxes.forEach((current) => {
          if (current !== checkbox) {
            current.checked = false;
          }
        });
      });
    });

    inputAltura.addEventListener('change', () => {
      const activa = document.querySelector('input[type=checkbox][name="medidaAltura"]:checked');

      if (!activa) {
        return;
      }

      console.log(`La altura ingresada es ${inputAltura.value} ${activa.value}`);
    });
  }

  const currentUrl = window.location.href;
  const tabs = ["inicio", "representantes", "familias", "gallos", "torneos", "peleas", "configuraciones", "usuarios"];

  tabs.forEach((tabName) => {
    if (currentUrl.indexOf(tabName + ".php") !== -1) {
      setActive("tab-" + tabName);
    }
  });

  function setActive(id) {
    const tab = document.getElementById(id);

    if (tab) {
      tab.setAttribute("class", "nav-item active");
    }
  }
})();
