<?php
include_once('../../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto_id = $_POST['id_producto'] ?? null;

    if (!$producto_id) {
        $response['success'] = false;
        $response['message'] = "No se ha especificado el producto a eliminar";
    } else {
        // Actualizar el estado del producto a 2 (eliminado) en la base de datos
        $stmt = $pdo->prepare("UPDATE productos SET estado = 2, fecha_actualizacion = CURRENT_TIMESTAMP WHERE ProductoID = :producto_id");
        $stmt->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
        $stmt->execute();
        $response['success'] = true;
        $response['message'] = "Producto eliminado correctamente";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Método no permitido";
}

echo json_encode($response);
exit;

