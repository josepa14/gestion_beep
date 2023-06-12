$(document).ready(function () {
    // AJAX PARA DATATABLES
    $.ajax({
        url: '/productos-json',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
      
            // Crea la tabla con los datos recibidos
            $('#tabla-productos').DataTable({
                data: response,
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'marca' },
                    { data: 'categoria' },
                    { data: 'subcategoria' },
                    { data: 'stock' },
                    { data: 'estado' },
                    { data: 'reservado' },
                    { data: 'recogido' },
                    { data: 'precio' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return '<a href="/productos/' + data.id + '/edit" class="btn btn-warning" bis_skin_checked="1">Editar</a><a href="/productos/' + data.id + '" class="btn btn-primary" bis_skin_checked="1">Ver</a>';
                        }
                    },
                    
                ],
                columnDefs: [
                    { targets: -1, orderable: false }
                ],
                language: {
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    paginate: {
                        first: 'Primero',
                        last: 'Último',
                        next: 'Siguiente',
                        previous: 'Anterior'
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"t>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',

            });
        }
    });
    // AJAX PARA NOTAS
    $.ajax({
        url: '/notas',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            var notas = response;
            var contenedor = $('#notas-container');

            // Iterar sobre las notas y agregarlas al contenedor
            $.each(notas, function (index, nota) {
                var notaElement = $('<div></div>')
                    .addClass('nota')
                    .attr('data-id', nota.id)
                    .html('<h4>' + nota.titulo + '</h4><p>' + nota.descripcion + '</p>');

                contenedor.append(notaElement);

                // Hacer la nota draggable dentro del contenedor
                notaElement.draggable({
                    containment: contenedor,
                    stack: '.nota',
                    snap: true,
                    snapMode: 'outer',
                    snapTolerance: 10
                });
            });
        }
    });
    // PARA MOSTRAR MAS DETALLES DE UNA NOTA
    $('#notas-container').on('click', '.nota', function () {
        var notaId = $(this).data("id");
        // Hacer una solicitud AJAX para obtener los detalles de la nota
        var titulo = $(this).find('h4').text();
        var descripcion = $(this).find('p').text();
        // Actualizar el contenido del modal con los detalles de la nota
        $("#modal-nota .modal-title").text(titulo);
        $("#modal-nota .modal-body").text(descripcion);
        $("#modal-nota #eliminar-nota").attr("data-id", notaId);
        $("#modal-nota").modal("show");
    });
    // PARA ELIMINAR UNA NOTA
    $("#modal-nota #eliminar-nota").on('click', function () {
        var notaId = $(this).attr("data-id");
        // Realizar una solicitud AJAX para eliminar la nota
        $.ajax({
            url: '/eliminar/' + notaId,
            type: 'POST',
            success: function (response) {
                // Eliminar la nota de la página
                $(".nota[data-id='" + notaId + "']").remove();
                $("#modal-nota").modal("hide");
            }
        });
    });

    $('#modal-nota').on('click', '.btn-secondary', function () {
        $('#modal-nota').modal('hide');
    });

    $('#modal-nota').on('click', '.close', function () {
        $('#modal-nota').modal('hide');
    });

    $('#btn-guardar-nota').on('click', function () {
        var titulo = $('#titulo').val();
        var descripcion = $('#descripcion').val();
        $('#modal-agregar-nota').modal('hide');
        // Realizar la solicitud AJAX para guardar la nota
        $.ajax({
            url: '/guardar-nota',
            type: 'POST',
            data: {
                titulo: titulo,
                descripcion: descripcion
            },
            dataType: 'json',
            success: function (response) {

                // Agregar la nueva nota al contenedor
                var notaElement = $('<div></div>')
                    .addClass('nota')
                    .attr('data-id', response.id)
                    .append('<h4>' + titulo + '</h4><p>' + descripcion + '</p>');

                $('#notas-container').append(notaElement);

                // Hacer la nota draggable dentro del contenedor
                notaElement.draggable({
                    containment: '#notas-container',
                    stack: '.nota',
                    snap: true,
                    snapMode: 'outer',
                    snapTolerance: 10
                });

                $('#titulo').val('');
                $('#descripcion').val('');
                $('#modal-agregar-nota').modal('hide');

            }
        });
    });

});




