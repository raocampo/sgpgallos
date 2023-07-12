
const checkboxes = document.querySelectorAll('input[type=checkbox][name="medida"]');
const inputPeso = document.querySelector('#peso');


// Deshabilitar el input 'peso' si no se selecciona ningún checkbox
checkboxes.forEach((checkbox) => {
  checkbox.addEventListener('change', () => {
    if (document.querySelectorAll('input[type=checkbox][name="medida"]:checked').length > 0) {
      inputPeso.disabled = false;
    } else {
      inputPeso.disabled = true;
    }
  });
});

// Asegurarse de que solo se pueda seleccionar un checkbox
checkboxes.forEach((checkbox) => {
  checkbox.addEventListener('change', () => {
    checkboxes.forEach((c) => {
      if (c !== checkbox) {
        c.checked = false;
      }
    });
  });
});

// Guardar el valor del input 'peso' y la unidad de medida correspondiente
inputPeso.addEventListener('change', () => {
  const medida = document.querySelector('input[type=checkbox][name="medida"]:checked').value;
  const peso = inputPeso.value;
  console.log(`El peso ingresado es ${peso} ${medida}`);
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