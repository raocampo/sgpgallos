document.getElementById('formCotejoManual').addEventListener('submit', function() {
  var checkboxes = document.getElementsByClassName('checkbox-pareja');
  var tableRows = document.querySelectorAll('#formCotejoManual table tbody tr');

  Array.from(checkboxes).forEach(function(checkbox) {
    if (checkbox.checked) {
      var galloId = checkbox.value;
      var row = document.querySelector('tr[data-id="' + galloId + '"]');
      if (row) {
        row.style.display = 'none';
      }
    }
  });
});

/*function validarFormulario() {
  var galloL = document.getElementById("galloL").value;
  var galloV = document.getElementById("galloV").value;
  var torneoId = document.getElementById("torneoId").value;

  // Verificar si los datos ya están seleccionados
  var registrosPrevios = obtenerRegistrosPrevios(); // Obtener registros previos (puedes implementar esta función según tu estructura)

  if (registrosPrevios.includes(galloL) && registrosPrevios.includes(galloV)) {
    // Los datos ya están seleccionados, mostrar mensaje de error
    alert("Los datos ya están seleccionados.");
    return false; // Evitar enviar el formulario
  }

  return true; // Permitir enviar el formulario
}*/



/*document.getElementById('formCotejoManual').addEventListener('submit', function(event) {
  // Obtener todos los checkboxes seleccionados
  var checkboxes = document.querySelectorAll('.checkbox-pareja:checked');
  
  // Verificar si hay al menos dos checkboxes seleccionados
  if (checkboxes.length < 2) {
    // Evitar el envío del formulario si no se seleccionaron al menos dos gallos
    event.preventDefault();
    alert('Debes seleccionar al menos dos gallos para poder cotejar.');
  }
});

$(document).ready(function() {
  // Capturar los checkboxes seleccionados y enviarlos al servidor
  $("#formCotejoManual").on("submit", function(e) {
    e.preventDefault(); // Evitar el envío del formulario

    var checkboxes = $(".checkbox-pareja:checked"); // Obtener los checkboxes seleccionados
    var parejaIds = checkboxes.map(function() {
      return $(this).val(); // Obtener el valor (ID de la pareja) de cada checkbox seleccionado
    }).get();

    // Enviar los datos mediante una solicitud AJAX
    $.ajax({
      url: $(this).attr("action"), // Ruta del servidor donde procesar los datos
      method: $(this).attr("method"), // Método HTTP (POST en este caso)
      data: { peleas: parejaIds }, // Datos a enviar (en este caso, los IDs de las parejas seleccionadas)
      success: function(response) {
        // Manejar la respuesta del servidor si es necesario
        console.log(response);
      },
      error: function(xhr, status, error) {
        // Manejar errores en la solicitud AJAX si es necesario
        console.log(error);
      }
    });
  });
});*/



