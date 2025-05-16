<?php
include_once '../app/config.php';

?>

<div id="main-wrapper">
    <?php

    include_once '../layout/parte1.php';
    ?>
    <!-- Select2 CSS -->

    <div class="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Venta de Planes</h4>
                        <h6 class="card-subtitle">Complete la información</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- <form action="../app/controllers/venta_planes/venta_plan.php" method="POST" class="form-horizontal m-t-30" enctype="multipart/form-data"> -->
                                <form id="crearPlanForm" class="form-horizontal m-t-30">
                                    <div class="form-group">
                                        <label for="buscar_cliente" class="col-sm-12">Buscar Cliente</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="buscar_cliente" placeholder="Ingrese nombre o DNI/RUC">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="id_cliente" class="col-sm-12">Seleccione un cliente</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" id="id_cliente" name="id_cliente" required>
                                                <?php
                                                $stmt = $pdo->prepare("SELECT id_cliente, nombre, dni_ruc FROM clientes WHERE estado != 2");
                                                $stmt->execute();
                                                echo "<option value='' selected>Seleccione un cliente</option>";
                                                while ($fila = $stmt->fetch()) {
                                                    echo "<option value='" . $fila['id_cliente'] . "'>" . $fila['nombre'] . " - " . $fila['dni_ruc'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="id_planes_servicios" class="col-sm-12">Seleccione un plan</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" id="id_planes_servicios" name="id_planes_servicios" required>
                                                <option value="" selected>Seleccione servicio</option>
                                                <?php
                                                $stmt = $pdo->prepare("SELECT id_plan_servicio, nombre_plan, tarifa_mensual, velocidad, igv_tarifa FROM planes_servicio WHERE estado = 1");
                                                $stmt->execute();
                                                while ($fila = $stmt->fetch()) {
                                                    $tarifa_mensual_igv = $fila['tarifa_mensual'] + $fila['igv_tarifa'];
                                                    echo "<option value='" . $fila['id_plan_servicio'] . "' data-precio='" . $fila['tarifa_mensual'] . "' data-velocidad='" . $fila['velocidad'] . "' >" . $fila['nombre_plan'] . " - S/." . $tarifa_mensual_igv . " - Velocidad: " . $fila['velocidad'] . "MB" . "</option>";
                                                }
                                                ?>
                                            </select>
                                            
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Ip" class="col-sm-12">IP</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="Ip" name="Ip" placeholder="Ingrese la IP" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Nombre_wifi" class="col-sm-12">Nombre de la red wifi</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="Nombre_wifi" name="Nombre_wifi" placeholder="Ingrese el nombre de la red wifi">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Contraseña_wifi" class="col-sm-12">Contraseña de la red wifi</label>
                                        <div class="col-sm-12">
                                            <input type="password" class="form-control" id="Contraseña_wifi" name="Contraseña_wifi" placeholder="Ingrese la contraseña de la red wifi">
                                        </div>
                                    </div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="Ubicacion" class="col-sm-12">Ubicación</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="Ubicacion" name="Ubicacion" placeholder="Ingrese la ubicación">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Foto_ubicacion" class="col-sm-12">Foto de la ubicación</label>
                                <div class="col-sm-12">
                                    <input type="file" class="form-control" id="Foto_ubicacion" name="Foto_ubicacion" accept="image/*">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Foto_router" class="col-sm-12">Foto del router</label>
                                <div class="col-sm-12">
                                    <input type="file" class="form-control" id="Foto_router" name="Foto_router" accept="image/*">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Fecha_inicio" class="col-sm-12">Fecha de inicio</label>
                                <div class="col-sm-12">
                                    <input type="date" class="form-control" id="Fecha_inicio" name="Fecha_inicio" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Fecha_finalizacion" class="col-sm-12">Fecha de finalización</label>
                                <div class="col-sm-12">
                                    <input type="date" class="form-control" id="Fecha_finalizacion" name="Fecha_finalizacion" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>" required>
                                </div>
                            </div>
                            <script>
                                document.getElementById('Fecha_inicio').addEventListener('change', function() {
                                    const fechaInicio = new Date(this.value);
                                    const fechaFinalizacion = document.getElementById('Fecha_finalizacion');
                                    fechaInicio.setMonth(fechaInicio.getMonth() + 1);
                                    const month = (fechaInicio.getMonth() + 1).toString().padStart(2, '0');
                                    const day = fechaInicio.getDate().toString().padStart(2, '0');
                                    fechaFinalizacion.value = `${fechaInicio.getFullYear()}-${month}-${day}`;
                                });
                            </script>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-warning" style="background-color: #EC672B; border-color: #EC672B;">Crear</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>


                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            // Almacenar las opciones originales al cargar la página
                            let opcionesOriginales = $('#id_cliente').html();

                            $('#buscar_cliente').on('input', function() {
                                const query = $(this).val();

                                if (query.length > 1) {
                                    $.ajax({
                                        url: 'buscar_cliente.php',
                                        method: 'POST',
                                        data: {
                                            query
                                        },
                                        success: function(response) {
                                            $('#id_cliente').html(response);
                                        },
                                        error: function() {
                                            alert('Error al buscar clientes.');
                                        }
                                    });
                                } else if (query.length === 0) {
                                    // Restablecer las opciones originales
                                    $('#id_cliente').html(opcionesOriginales);
                                }
                            });
                        });
                    </script>

                    <!-- Contenedor para mensajes -->
                    <div id="mensajeExito" style="display:none; color:green; font-weight:bold;"></div>

                    <!-- JavaScript -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $("#crearPlanForm").on("submit", function(e) {
                                e.preventDefault(); // Evitar que se recargue la página

                                // Crear un formulario con los datos del formulario
                                const formData = new FormData();
                                formData.append('id_cliente', $("#id_cliente").val());
                                formData.append('id_planes_servicios', $("#id_planes_servicios").val());
                                formData.append('Ip', $("#Ip").val());
                                formData.append('Nombre_wifi', $("#Nombre_wifi").val());
                                formData.append('Contraseña_wifi', $("#Contraseña_wifi").val());
                                formData.append('Ubicacion', $("#Ubicacion").val());
                                formData.append('Foto_ubicacion', $("#Foto_ubicacion")[0].files[0]);
                                formData.append('Foto_router', $("#Foto_router")[0].files[0]);
                                formData.append('Fecha_inicio', $("#Fecha_inicio").val());
                                formData.append('Fecha_finalizacion', $("#Fecha_finalizacion").val());

                                // Enviar datos usando AJAX
                                $.ajax({
                                    url: "../app/controllers/venta_planes/venta_plan.php", // Archivo PHP que procesa la solicitud
                                    method: "POST",
                                    contentType: false,
                                    processData: false,
                                    data: formData, // Enviar el formulario con los datos
                                    success: function(respuesta) {
                                        // Mostrar mensaje de éxito
                                        $("#mensajeExito").text("Guardado correctamente").show();

                                        // Limpiar formulario
                                        $("#crearPlanForm")[0].reset();
                                    },
                                    error: function() {
                                        alert("Ocurrió un error al guardar el plan.");
                                    }
                                });
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include('../layout/parte2.php'); ?>