(() => {
  const checkboxes = document.querySelectorAll('input[type=checkbox][name="medida"]');
  const inputPeso = document.querySelector('#peso');

  if (checkboxes.length && inputPeso) {
    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        if (document.querySelectorAll('input[type=checkbox][name="medida"]:checked').length > 0) {
          inputPeso.disabled = false;
        } else {
          inputPeso.disabled = true;
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

    inputPeso.addEventListener('change', () => {
      const activa = document.querySelector('input[type=checkbox][name="medida"]:checked');

      if (!activa) {
        return;
      }

      console.log(`El peso ingresado es ${inputPeso.value} ${activa.value}`);
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
