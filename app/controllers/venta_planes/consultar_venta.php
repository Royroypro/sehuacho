<?php


try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT c.id, c.id_cliente, c.id_planes_servicios, c.Ip, c.Nombre_wifi, c.Contraseña_wifi, c.Ubicacion, c.Foto_ubicacion, c.Foto_router, c.Fecha_inicio, c.Fecha_finalizacion, c.Estado, p.nombre_plan, p.tarifa_mensual, p.velocidad FROM cliente_planes c INNER JOIN planes_servicio p ON c.id_planes_servicios = p.id_plan_servicio WHERE c.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $venta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($venta) {
            $response['success'] = true;
            $response['data'] = $venta;
        } else {
            $response['success'] = false;
            $response['message'] = "No se encontr  la venta con id: " . $id;
        }
    } else {
        $response['success'] = false;
        $response['message'] = "No se ha especificado el id de la venta";
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = "Error: " . $e->getMessage();
}



