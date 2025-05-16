<?php
include_once('../../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_venta = $_POST['id_venta'] ?? null;

    if (!$id_venta) {
        $response['success'] = false;
        $response['message'] = "No se ha especificado el id de la venta a eliminar";
    } else {
        // Actualizar el estado del plan a 2 (eliminado) en la base de datos
        $stmt = $pdo->prepare("UPDATE `cliente_planes` SET `Estado` = 2, `Fecha_finalizacion` = CURRENT_TIMESTAMP WHERE `id` = :id_venta");
        $stmt->bindParam(':id_venta', $id_venta);
        $stmt->execute(); // Ejecutar la consulta para eliminar el plan
        $response['success'] = true;
        $response['message'] = "Plan eliminado correctamente";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Método no permitido";
}


echo json_encode($response);
exit;

