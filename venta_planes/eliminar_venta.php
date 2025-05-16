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
            $stmt->execute();
            $venta = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($venta) {
                $stmt_cliente = $pdo->prepare("SELECT nombre, dni_ruc FROM clientes WHERE id_cliente = :id_cliente");
                $stmt_cliente->bindParam(':id_cliente', $venta['id_cliente']);
                $stmt_cliente->execute();
                $cliente_data = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
                $nombre_cliente = htmlspecialchars($cliente_data['nombre'], ENT_QUOTES, 'UTF-8');
                $dni_ruc_cliente = htmlspecialchars($cliente_data['dni_ruc'], ENT_QUOTES, 'UTF-8');

                $stmt_plan = $pdo->prepare("SELECT nombre_plan, tarifa_mensual, velocidad FROM planes_servicio WHERE id_plan_servicio = :id_plan_servicio");
                $stmt_plan->bindParam(':id_plan_servicio', $venta['id_planes_servicios']);
                $stmt_plan->execute();
                $plan_data = $stmt_plan->fetch(PDO::FETCH_ASSOC);
                $nombre_plan = htmlspecialchars($plan_data['nombre_plan'], ENT_QUOTES, 'UTF-8');
                $precio_plan = htmlspecialchars($plan_data['tarifa_mensual'], ENT_QUOTES, 'UTF-8');
                $velocidad_plan = htmlspecialchars($plan_data['velocidad'], ENT_QUOTES, 'UTF-8');

                $fecha_venta = htmlspecialchars($venta['Fecha_inicio'], ENT_QUOTES, 'UTF-8');
            }
        }
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Eliminar Venta</h4>
                        <h6 class="card-subtitle">¿Está seguro de eliminar la venta?</h6>
                        <form id="eliminarVentaForm" class="form-horizontal m-t-30" method="post">
                            <input type="hidden" id = "id_venta" name="id_venta" value="<?php echo $id; ?>>
                            <div class="form-group">
                                <label for="nombre_cliente" class="col-sm-12">Cliente</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" placeholder="Ingrese el nombre del cliente" value="<?php echo htmlspecialchars($nombre_cliente, ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($dni_ruc_cliente, ENT_QUOTES, 'UTF-8'); ?>" required readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="nombre_plan" class="col-sm-12">Nombre del plan</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="nombre_plan" name="nombre_plan" placeholder="Ingrese el nombre del plan" value="<?php echo htmlspecialchars($nombre_plan, ENT_QUOTES, 'UTF-8') . ' - S/.' . htmlspecialchars($precio_plan, ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($velocidad_plan, ENT_QUOTES, 'UTF-8').'MB'; ?>" required readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="fecha_venta" class="col-sm-12">Fecha de venta</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="fecha_venta" name="fecha_venta" placeholder="Ingrese la fecha de venta" value="<?php echo $fecha_venta; ?>" required readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </div>
                            </div>
                        </form>

                        <!-- Contenedor para mensajes -->
                        <div id="mensajeExito" style="display:none; color:green; font-weight:bold;" aria-live="polite"></div>

                        <!-- JavaScript -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            $(document).ready(function() {
                                $("#eliminarVentaForm").on("submit", function(e) {
                                    e.preventDefault(); // Evitar que se recargue la página

                                    // Obtener datos del formulario
                                    const datos = {
                                        id_venta: $("input[name='id_venta']").val()
                                    };

                                    // Enviar datos usando AJAX
                                    $.ajax({
                                        url: "../app/controllers/venta_planes/eliminar_venta.php", // Archivo PHP que procesa la solicitud
                                        method: "POST",
                                        data: datos, // Enviar datos
                                        dataType: "json", // Asegurarse de recibir JSON
                                        success: function(respuesta) {
                                            if (respuesta && respuesta.success) {
                                                $("#mensajeExito").text(respuesta.message).show();
                                                setTimeout(function() {
                                                    window.location.href = "lista_venta.php";
                                                }, 2000);
                                            } else {
                                                alert(respuesta ? respuesta.message : "Error inesperado.");
                                            }
                                        },
                                        error: function() {
                                            alert("Ocurrió un error al eliminar la venta.");
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

