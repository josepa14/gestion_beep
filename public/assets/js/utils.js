$(document).ready(function() {
  $('.carousel').carousel();
  $(".panel").hide();
  $(".flip").click(function() {
    $(this).find(".panel").slideToggle(400);
  });



});
function noDisponible(message) {
  $.toast({
    text: '<h6 class="text-light">¡Esta ventana aún no está disponible!</h6>',
    position: "top-right",
    showHideTransition: 'fade',
    icon: 'warning'
  });
}
function obtenerFechaActual() {
  var fecha = new Date();
  var dia = fecha.getDate();
  var mes = fecha.getMonth() + 1; // Los meses comienzan en 0, por lo que se suma 1
  var anio = fecha.getFullYear();

  // Asegura que el día y el mes tengan dos dígitos
  dia = dia < 10 ? '0' + dia : dia;
  mes = mes < 10 ? '0' + mes : mes;

  // Formato: dd/mm/yyyy
  var fechaActual = dia + '/' + mes + '/' + anio;

  return fechaActual;
}