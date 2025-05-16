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
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Detalle de la Venta</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                                <tr>
                                                    <td>Cliente</td>
                                                    <td>
                                                        <?php
                                                            $stmt = $pdo->prepare("SELECT nombre, dni_ruc FROM clientes WHERE id_cliente = :id_cliente");
                                                            $stmt->execute(['id_cliente' => $id_cliente]);
                                                            $cliente = $stmt->fetch();
                                                            echo $cliente['nombre'] . ' - ' . $cliente['dni_ruc'];
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Detalles del Plan</td>
                                                    <td>
                                                        <?php
                                                            $stmt_plan = $pdo->prepare("SELECT nombre_plan, tarifa_mensual, velocidad, igv_tarifa FROM planes_servicio WHERE id_plan_servicio = :id_planes_servicios");
                                                            $stmt_plan->execute(['id_planes_servicios' => $id_planes_servicios]);
                                                            $plan = $stmt_plan->fetch();
                                                            $tarifa_mensual_igv = $plan['tarifa_mensual'] + $plan['igv_tarifa'];
                                                            echo $plan['nombre_plan'] . ' - S/.' . $tarifa_mensual_igv . ' - ' . $plan['velocidad'] . 'MB';
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Ip</td>
                                                    <td><?php echo $Ip; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Nombre del Wifi</td>
                                                    <td><?php echo $Nombre_wifi; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Contraseña del Wifi</td>
                                                    <td><?php echo $Contraseña_wifi; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Ubicación</td>
                                                    <td><?php echo $Ubicacion; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Fecha de Inicio</td>
                                                    <td><?php echo $Fecha_inicio; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Fecha de Finalización</td>
                                                    <td><?php echo $Fecha_finalizacion; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Estado</td>
                                                    <td><?php echo $Estado == '1' ? '<span style="color:green;">Activo</span>' : '<span style="color:red;">Inactivo</span>'; ?></td>
                                                </tr>


                                                <tr>
                                                    <td>Foto de la ubicación</td>
                                                    <td><img src="<?php echo $URL . "app/controllers/venta_planes/fotos_ubicacion/" . $Foto_ubicacion; ?>" width="100" height="100" class="img-thumbnail" /></td>
                                                </tr>
                                                <tr>
                                                    <td>Foto del router</td>
                                                    <td><img src="<?php echo $URL . "app/controllers/venta_planes/fotos_router/" . $Foto_router; ?>" width="100" height="100" class="img-thumbnail" /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-left">
                    <a href="lista_venta.php" class="btn btn-primary">Regresar</a>
                </div>
                <br>
            </div>
        </div>
</div>

<?php include('../layout/parte2.php'); ?>
