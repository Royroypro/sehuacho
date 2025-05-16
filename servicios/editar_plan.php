<?php
include_once '../app/config.php';
include_once '../app/controllers/planes/consultar_plan.php';
?>
<div id="main-wrapper">
    <?php

    include_once '../layout/parte1.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
    ?>

    <div class="page-wrapper">
    <?php
    if ($id) {
        $stmt = $pdo->prepare("SELECT nombre_plan, tarifa_mensual, descripcion, velocidad, codigo_plan, igv_tarifa FROM planes_servicio WHERE id_plan_servicio = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $plan = $stmt->fetch();
        if ($plan) {
            $nombre = htmlspecialchars($plan['nombre_plan'], ENT_QUOTES, 'UTF-8');
            $tarifa_mensual = htmlspecialchars($plan['tarifa_mensual'], ENT_QUOTES, 'UTF-8');
            $descripcion = htmlspecialchars($plan['descripcion'], ENT_QUOTES, 'UTF-8');
            $velocidad = htmlspecialchars($plan['velocidad'], ENT_QUOTES, 'UTF-8');
            $codigo_plan = htmlspecialchars($plan['codigo_plan'], ENT_QUOTES, 'UTF-8');
            $igv_tarifa = htmlspecialchars($plan['igv_tarifa'], ENT_QUOTES, 'UTF-8');
        }
    }
?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Editar Plan</h4>
                        <h6 class="card-subtitle">Complete la información</h6>
                        <form id="editarPlanForm" class="form-horizontal m-t-30">
                            <input type="hidden" name="id_plan_servicio" value="<?php echo $id; ?>">
                            
                            <div class="form-group">
                                <label for="nombre" class="col-sm-12">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="nombre" name="nombre_plan" placeholder="Ingrese el nombre del plan" value="<?php echo $nombre; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="costo" class="col-sm-12">Tarifa mensual S/.</label>
                                <div class="col-sm-12">
                                    <input type="number" step="0.01" class="form-control" id="costo" name="tarifa_mensual" placeholder="Ingrese la tarifa mensual del plan" value="<?php echo $tarifa_mensual + $igv_tarifa; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="descripcion" class="col-sm-12">Descripción</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Ingrese una descripción del plan" required><?php echo $descripcion; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="velocidad" class="col-sm-12">Velocidad</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="velocidad" name="velocidad" placeholder="Ingrese la velocidad del plan" value="<?php echo $velocidad; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="codigo" class="col-sm-12">Código</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="codigo" name="codigo_plan" placeholder="El código se generará automáticamente" readonly required value="<?php echo $plan['codigo_plan']; ?>">
                                </div>
                            </div>
                            <input type="hidden" id="id_plan" name="id_plan" value="<?php echo $id; ?>">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-warning" style="background-color: #EC672B; border-color: #EC672B;">Actualizar</button>
                                </div>
                            </div>
                        </form>
                    

                        <!-- Contenedor para mensajes -->
                        <div id="mensajeExito" style="display:none; color:green; font-weight:bold;"></div>

                        <!-- JavaScript -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            $(document).ready(function () {
                                $("#editarPlanForm").on("submit", function (e) {
                                    e.preventDefault(); // Evitar que se recargue la página

                                    // Obtener datos del formulario
                                    const datos = {
                                        id_plan: $("#id_plan").val(),
                                        nombre_plan: $("#nombre").val(),
                                        tarifa_mensual: $("#costo").val(),
                                        descripcion: $("#descripcion").val(),
                                        velocidad: $("#velocidad").val(),
                                        codigo_plan: $("#codigo").val()
                                    };

                                    // Enviar datos usando AJAX
                                    $.ajax({
                                        url: "../app/controllers/planes/editar_plan.php", // Archivo PHP que procesa la solicitud
                                        method: "POST",
                                        contentType: "application/json",
                                        data: JSON.stringify(datos), // Convertir datos a JSON
                                        success: function (respuesta) {
                                            // Mostrar mensaje de éxito
                                            $("#mensajeExito").text("Guardado correctamente").show();
                                            
                                            // Actualizar el formulario
                                            $("#editarPlanForm").load("../planes/editar_plan.php?id="+datos.id_plan+" #editarPlanForm > *");
                                        },
                                        error: function () {
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
