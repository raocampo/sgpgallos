   // Obtiene los checkboxes de medida
    //const medidas = document.querySelectorAll('.medida');
    
    // Recorre los checkboxes de medida y agrega un listener de cambio
    //medidas.forEach(medida => {
      //  medida.addEventListener('change', () => {
            // Obtiene el valor del atributo data-medida del checkbox seleccionado
        //    const medidaSeleccionada = medida.getAttribute('data-medida');
            
            // Desactiva todos los inputs de peso
          //  document.querySelectorAll('input[name="peso"]').forEach(peso => {
            //    peso.style.display = 'none';
            //});
            
            // Activa el input correspondiente al checkbox seleccionado
            //document.querySelector(`input[name="peso"][data-medida="${medidaSeleccionada}"]`).style.display = 'block';
        //});
    //});

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

