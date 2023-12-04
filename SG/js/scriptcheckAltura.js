
const checkboxes1 = document.querySelectorAll('input[type=checkbox][name="medidaAltura"]');
const inputAltura = document.querySelector('#altura');


// Deshabilitar el input 'altura' si no se selecciona ningún checkbox
checkboxes1.forEach((checkbox) => {
  checkbox.addEventListener('change', () => {
    if (document.querySelectorAll('input[type=checkbox][name="medidaAltura"]:checked').length > 0) {
      inputAltura.disabled = false;
    } else {
      inputAltura.disabled = true;
    }
  });
});

// Asegurarse de que solo se pueda seleccionar un checkbox
checkboxes1.forEach((checkbox) => {
  checkbox.addEventListener('change', () => {
    checkboxes1.forEach((c) => {
      if (c !== checkbox) {
        c.checked = false;
      }
    });
  });
});

// Guardar el valor del input 'altura' y la unidad de medida correspondiente
inputAltura.addEventListener('change', () => {
  const medida = document.querySelector('input[type=checkbox][name="medidaAltura"]:checked').value;
  const altura = inputAltura.value;
  console.log(`La altura ingresada es ${altura} ${medida}`);
});

/// Url actual
let url = window.location.href;

/// Elementos de li
const tabs = ["inicio", "representantes", "familias", "gallos", "torneos", "peleas", "configuraciones", "usuarios"];

tabs.forEach(e => {
    /// Agregar .php y ver si lo contiene en la url
    if (url.indexOf(e + ".php") !== -1) {
        /// Agregar tab- para hacer que coincida la Id
        setActive("tab-" + e);
    }

});

/// Funcion que asigna la clase active
function setActive(id) {
    document.getElementById(id).setAttribute("class", "nav-item active");
}