$(document).ready(function () {
  $('.card-reservas').click(function () {
    $(this).find('.modal').modal('show');
  });

  $('.reservar-btn').click(function (e) {
    e.preventDefault();
    var reservarBtn = $(this);
    if (reservarBtn.hasClass('btn-disabled')) {
      return; // Si el botón ya está deshabilitado, no hacer nada
    }

    var productoId = reservarBtn.data('id');
    var fechaReserva = obtenerFechaActual();
    var crearReservaUrl = reservarBtn.data('crear-reserva-url');

    reservarBtn.addClass('btn-disabled'); // Agregar clase al botón para deshabilitarlo

    // Realizar una solicitud AJAX al controlador para crear la reserva
    $.ajax({
      url: crearReservaUrl,
      method: 'POST',
      data: {
        productoId: productoId,
        fechaReserva: fechaReserva
      },
      success: function (response) {
        // La reserva se creó con éxito
        // Aquí puedes realizar acciones adicionales, como mostrar una notificación o actualizar la interfaz de usuario
        console.log('Reserva creada:', response);
        alert('Tu reserva se ha realizado correctamente.'); // Mostrar mensaje al usuario
        window.location.href = "/reservas";
      },
      error: function (xhr, status, error) {
        console.error('Error al crear la reserva:', error);
        $.toast({
          heading: 'Error',
          text: 'Ocurrió un error al crear la reserva.',
          icon: 'error',
          position: 'bottom-right',
          afterHidden: function () {
            location.reload(); // Recargar la página de reservas
          }
        });
      },
    });
  });

  // SOLICITUD AJAX PARA ANULAR UNA RESERVA
  $('.cancelar-reserva').click(function () {
    console.log("aqui lluego")
    var reservaId = $(this).data('reserva-id');
    console.log(reservaId)
    if (confirm('¿Estás seguro de que deseas cancelar la reserva?')) {
      cancelarReserva(reservaId);
    }
  });

  function cancelarReserva(reservaId) {
    $.ajax({
      url: '/cancelar_reserva',
      method: 'POST',
      data: { reservaId: reservaId },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert('Reserva cancelada exitosamente.');
          window.location.reload();
        } else {
          alert('Ha ocurrido un error al cancelar la reserva. Por favor, inténtalo nuevamente.');
        }
      },
      error: function (error) {
        alert('Ha ocurrido un error al cancelar la reserva. Por favor, inténtalo nuevamente.');
        console.error(error);
      }
    });
  }

  // SOLICITUD AJAX PARA CONFIRMAR UNA RESERVA
  $('.confirmar-venta').click(function () {
    var reservaId = $(this).data('reserva-id');
    if (confirm('¿Estás seguro de confirmar la venta?')) {
      confirmarVenta(reservaId);
    }
  });

  function confirmarVenta(reservaId) {
    $.ajax({
      url: '/confirmar_venta',
      method: 'POST',
      data: { reservaId: reservaId },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert('Venta confirmada exitosamente.');
          location.reload();
        } else {
          alert('Ha ocurrido un error al confirmar la venta. Por favor, inténtalo nuevamente 1.');
        }
      },
      error: function (error) {
        alert('Ha ocurrido un error al confirmar la venta. Por favor, inténtalo nuevamente 2.');
        console.error(error);
      }
    });
  }
});