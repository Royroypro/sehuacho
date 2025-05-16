<?php
include_once '../app/config.php';

?>

<div id="main-wrapper">
    <?php
    include_once '../layout/parte1.php';
    ?>
    <div class="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Crear Servicio</h4>
                        <h6 class="card-subtitle">Complete la información</h6>
                        <form id="crearServicioForm" class="form-horizontal m-t-30">
                            <div class="form-group">
                                <label for="nombre" class="col-sm-12">Nombre</label>
                                <div class="col-sm-12">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="nombre"
                                        name="nombre_servicio"
                                        placeholder="Ingrese el nombre del servicio"
                                        required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion" class="col-sm-12">Descripción</label>
                                <div class="col-sm-12">
                                    <textarea
                                        class="form-control"
                                        id="descripcion"
                                        name="descripcion"
                                        placeholder="Ingrese una descripción del servicio"
                                        required></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="unidad_medida" class="col-sm-12">Unidad de Medida</label>
                                <div class="col-sm-12">
                                    <select
                                        class="form-control"
                                        id="unidad_medida"
                                        name="unidad_medida"
                                        required>
                                        <option value="">Seleccione una unidad de medida</option>
                                        <option value="NIU">Unidad</option>
                                        <option value="KG">Kilogramo</option>
                                        <option value="LTS">Litro</option>
                                        <option value="M">Metro</option>
                                        <option value="M2">Metro Cuadrado</option>
                                        <option value="M3">Metro Cúbico</option>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="precio_unitario" class="col-sm-12">Precio Unitario S/.</label>
                                <div class="col-sm-12">
                                    <input
                                        type="number"
                                        step="0.01"
                                        class="form-control"
                                        id="precio_unitario"
                                        name="precio_unitario"
                                        placeholder="Ingrese el precio unitario del servicio"
                                        required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="codigo" class="col-sm-12">Código</label>
                                <div class="col-sm-12">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="codigo"
                                        name="codigo_servicio"
                                        placeholder="El código se generará automáticamente"
                                        readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button
                                        type="submit"
                                        class="btn btn-warning"
                                        style="background-color: #EC672B; border-color: #EC672B;">
                                        Crear
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Contenedor para mensajes -->
                        <div id="mensajeExito" style="display:none; color:green; font-weight:bold;"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../layout/parte2.php'; ?>

<script>
    $(document).ready(function() {
        // Generar código automático al tipear el nombre
        $('#nombre').on('input', function() {
            const nombre = $(this).val().trim();
            if (nombre.length >= 2) {
                const letras = nombre.substring(0, 2).toUpperCase();
                const numero = Math.floor(10000000 + Math.random() * 90000000);
                $('#codigo').val(`${letras}${numero}`);
            } else {
                $('#codigo').val('');
            }
        });

        // Envío de formulario vía AJAX
        $('#crearServicioForm').on('submit', function(e) {
            e.preventDefault();

            // Recopilar datos
            const datos = {
                nombre_servicio: $('#nombre').val().trim(),
                descripcion: $('#descripcion').val().trim(),
                unidad_medida: $('#unidad_medida').val(),
                precio_unitario: $('#precio_unitario').val().trim(),
                codigo_servicio: $('#codigo').val().trim()
            };

            // Validar
            let faltan = [];
            for (let key in datos) {
                if (!datos[key]) faltan.push(key.replace('_', ' '));
            }
            if (faltan.length) {
                alert('Por favor, complete: ' + faltan.join(', '));
                return;
            }

            $.ajax({
                url: '../app/controllers/servicios/crear_servicio.php',
                type: 'POST',
                dataType: 'json',
                data: datos,
                success: function(res) {
                    if (res.success) {
                        $('#mensajeExito')
                            .text(res.message || 'Guardado correctamente')
                            .show();
                        $('#crearServicioForm')[0].reset();
                        $('#codigo').val('');
                    } else {
                        alert(res.message || 'Error al guardar el servicio.');
                    }
                },
                error: function(xhr, status, err) {
                    console.error('AJAX Error:', status, err);
                    alert('Ocurrió un error. Intenta nuevamente.');
                }
            });
        });
    });
</script>
