<?php
include_once '../app/config.php';
include_once '../app/controllers/venta_planes/consultar_venta.php';
?>
<div id="main-wrapper">
    <?php

    include_once '../layout/parte1.php';

    $id = isset($_GET['id']) ? $_GET['id'] : null;
    ?>

    <div class="page-wrapper">
        <?php
        if ($id) {

            $id_cliente = isset($venta['id_cliente']) ? htmlspecialchars($venta['id_cliente'], ENT_QUOTES, 'UTF-8') : null;
            $id_planes_servicios = isset($venta['id_planes_servicios']) ? htmlspecialchars($venta['id_planes_servicios'], ENT_QUOTES, 'UTF-8') : null;
            $Ip = isset($venta['Ip']) ? htmlspecialchars($venta['Ip'], ENT_QUOTES, 'UTF-8') : null;
            $Nombre_wifi = isset($venta['Nombre_wifi']) ? htmlspecialchars($venta['Nombre_wifi'], ENT_QUOTES, 'UTF-8') : null;
            $Contraseña_wifi = isset($venta['Contraseña_wifi']) ? htmlspecialchars($venta['Contraseña_wifi'], ENT_QUOTES, 'UTF-8') : null;
            $Ubicacion = isset($venta['Ubicacion']) ? htmlspecialchars($venta['Ubicacion'], ENT_QUOTES, 'UTF-8') : null;
            $Foto_ubicacion = isset($venta['Foto_ubicacion']) ? htmlspecialchars($venta['Foto_ubicacion'], ENT_QUOTES, 'UTF-8') : null;
            $Foto_router = isset($venta['Foto_router']) ? htmlspecialchars($venta['Foto_router'], ENT_QUOTES, 'UTF-8') : null;
            $Fecha_inicio = isset($venta['Fecha_inicio']) ? htmlspecialchars($venta['Fecha_inicio'], ENT_QUOTES, 'UTF-8') : null;
            $Fecha_finalizacion = isset($venta['Fecha_finalizacion']) ? htmlspecialchars($venta['Fecha_finalizacion'], ENT_QUOTES, 'UTF-8') : null;
            $Estado = isset($venta['Estado']) ? htmlspecialchars($venta['Estado'], ENT_QUOTES, 'UTF-8') : null;

        }


        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Editar venta de Plan</h4>
                        <h6 class="card-subtitle">Complete la información</h6>
                        <!--  <form action="../app/controllers/venta_planes/editar_venta.php" method="POST" class="form-horizontal m-t-30" enctype="multipart/form-data"> -->
                        <form id="editarPlanForm" method="POST" class="form-horizontal m-t-30" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Estado" class="col-sm-12">Estado</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" id="Estado" name="Estado" required>
                                                <option value="1" <?php echo ($Estado == "1") ? "selected" : ""; ?>>Activo</option>
                                                <option value="0" <?php echo ($Estado == "0") ? "selected" : ""; ?>>Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" id ="id" name="id" value="<?php echo $id; ?>">
                                    <div class="form-group">
                                        <label for="id_cliente" class="col-sm-12">Cliente</label>
                                        <div class="col-sm-12">
                                            <?php
                                            $stmt = $pdo->prepare("SELECT id_cliente, nombre FROM clientes WHERE id_cliente = :id_cliente");
                                            $stmt->bindParam(':id_cliente', $id_cliente);
                                            $stmt->execute();
                                            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
                                            ?>
                                            <input type="text" class="form-control" id="id_cliente" name="id_cliente" value="<?php echo $fila['nombre']; ?>" readonly>
                                            <input type="hidden" id="id_cliente_hidden" name="id_cliente" value="<?php echo $id_cliente; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Ip" class="col-sm-12">IP</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="Ip" name="Ip" value="<?php echo $Ip; ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Nombre_wifi" class="col-sm-12">Nombre de la red wifi</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="Nombre_wifi" name="Nombre_wifi" value="<?php echo $Nombre_wifi; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Contraseña_wifi" class="col-sm-12">Contraseña de la red wifi</label>
                                        <div class="col-sm-12">
                                            <input type="password" class="form-control" id="Contraseña_wifi" name="Contraseña_wifi" value="<?php echo $Contraseña_wifi; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                                                    if ($fila['id_plan_servicio'] == $id_planes_servicios) {
                                                        echo "<option value='" . $fila['id_plan_servicio'] . "' data-precio='" . $tarifa_mensual_igv . "' data-velocidad='" . $fila['velocidad'] . "' selected>" . $fila['nombre_plan'] . " - S/." . $tarifa_mensual_igv . " - Velocidad: " . $fila['velocidad'] . "MB" . "</option>";
                                                    } else {
                                                        echo "<option value='" . $fila['id_plan_servicio'] . "' data-precio='" . $tarifa_mensual_igv . "' data-velocidad='" . $fila['velocidad'] . "'>" . $fila['nombre_plan'] . " - S/." . $tarifa_mensual_igv . " - Velocidad: " . $fila['velocidad'] . "MB" . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Ubicacion" class="col-sm-12">Ubicación</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="Ubicacion" name="Ubicacion" value="<?php echo $Ubicacion; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Foto_ubicacion" class="col-sm-12">Foto de la ubicación</label>
                                        <div class="col-sm-12">
                                            <img src="<?php echo $URL . "app/controllers/venta_planes/fotos_ubicacion/" . $Foto_ubicacion; ?>" width="100" height="100" class="img-thumbnail" id="imgFotoUbicacion" />
                                            <input type="file" class="form-control" id="Foto_ubicacion" name="Foto_ubicacion" accept="image/*" onchange="document.getElementById('imgFotoUbicacion').src = window.URL.createObjectURL(this.files[0])">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Foto_router" class="col-sm-12">Foto del router</label>
                                        <div class="col-sm-12">
                                            <img src="<?php echo $URL . "app/controllers/venta_planes/fotos_router/" . $Foto_router; ?>" width="100" height="100" class="img-thumbnail" id="imgFotoRouter" />
                                            <input type="file" class="form-control" id="Foto_router" name="Foto_router" accept="image/*" onchange="document.getElementById('imgFotoRouter').src = window.URL.createObjectURL(this.files[0])">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Fecha_inicio" class="col-sm-12">Fecha de inicio</label>
                                        <div class="col-sm-12">
                                            <input type="date" class="form-control" id="Fecha_inicio" name="Fecha_inicio" value="<?php echo date('Y-m-d', strtotime($Fecha_inicio)); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Fecha_finalizacion" class="col-sm-12">Fecha de finalización</label>
                                        <div class="col-sm-12">
                                            <input type="date" class="form-control" id="Fecha_finalizacion" name="Fecha_finalizacion" value="<?php echo date('Y-m-d', strtotime($Fecha_finalizacion)); ?>" required>
                                        </div>
                                    </div>
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
                                    <button type="submit" class="btn btn-warning" style="background-color: #EC672B; border-color: #EC672B;">Actualizar</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Contenedor para mensajes -->
                    <div id="mensajeExito" style="display:none; color:green; font-weight:bold;"></div>

                    <!-- JavaScript -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $("#editarPlanForm").on("submit", function(e) {
                                e.preventDefault(); // Evitar que se recargue la página

                                // Crear un FormData para enviar archivos
                                const formData = new FormData(this);

                                // Enviar datos usando AJAX
                                $.ajax({
                                    url: "../app/controllers/venta_planes/editar_venta.php", // Archivo PHP que procesa la solicitud
                                    method: "POST",
                                    contentType: false,
                                    processData: false,
                                    data: formData, // Enviar datos del formulario
                                    success: function(respuesta) {
                                        // Mostrar mensaje de éxito
                                        $("#mensajeExito").text("Guardado correctamente").show();

                                        // Actualizar el formulario
                                        $("#editarPlanForm").load("../venta_planes/editar_venta.php?id=" + formData.get('id') + " #editarPlanForm > *");
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