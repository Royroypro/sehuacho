<?php
require '../app/config.php'; // Asegúrate de incluir tu archivo de conexión
include_once '../layout/sesion.php';
if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $stmt = $pdo->prepare("SELECT c.id_cliente, c.nombre, c.apellido_paterno, c.apellido_materno, c.dni_ruc, c.tipo_documento, c.celular, c.email, c.direccion, c.referencia, p.nombre_plan, p.tarifa_mensual, p.velocidad, p.igv_tarifa FROM clientes c JOIN cliente_planes cp ON c.id_cliente = cp.id_cliente JOIN planes_servicio p ON cp.id_planes_servicios = p.id_plan_servicio WHERE c.estado = 1 AND cp.estado = 1 AND (c.nombre LIKE :query OR c.apellido_paterno LIKE :query OR c.apellido_materno LIKE :query OR c.dni_ruc LIKE :query OR c.tipo_documento LIKE :query OR c.celular LIKE :query OR c.email LIKE :query OR c.direccion LIKE :query OR c.referencia LIKE :query OR p.nombre_plan LIKE :query OR p.tarifa_mensual LIKE :query OR p.velocidad LIKE :query)");
    $stmt->execute([':query' => "%$query%"]);
    $options = "";
    while ($fila = $stmt->fetch()) {
        $precioConIgv = $fila['tarifa_mensual'] + $fila['igv_tarifa'];
        $options .= "<option value='" . $fila['id_cliente'] . "' data-precio='" . $fila['tarifa_mensual'] . "' data-igv_tarifa='" . $fila['igv_tarifa'] . "' data-tipo_documento='" . $fila['tipo_documento'] . "'>" . $fila['nombre'] . " " . $fila['apellido_paterno'] . " " . $fila['apellido_materno'] . " - " . $fila['tipo_documento'] . " " . $fila['dni_ruc'] . " - " . $fila['nombre_plan'] . " - S/." . $precioConIgv . " - Velocidad: " . $fila['velocidad'] . "MB</option>";
    }

    echo $options ?: "<option value=''>No se encontraron resultados</option>";
}
?>

