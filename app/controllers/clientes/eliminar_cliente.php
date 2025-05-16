<?php
include_once('../../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cliente = $_POST['id_cliente'] ?? null;

    if (!$id_cliente) {
        $response['success'] = false;
        $response['message'] = "No se ha especificado el cliente a eliminar";
    } else {
        // Actualizar el estado del cliente a 2 (eliminado) en la base de datos sin la columna 'fecha_actualizacion'
        $stmt = $pdo->prepare("UPDATE clientes SET estado = 2 WHERE id_cliente = :id_cliente");
        $stmt->bindParam(':id_cliente', $id_cliente);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Cliente eliminado correctamente";
        } else {
            $response['success'] = false;
            $response['message'] = "Error al eliminar el cliente";
        }
    }
} else {
    $response['success'] = false;
    $response['message'] = "Método no permitido";
}

header('Content-Type: application/json');
echo json_encode($response);
exit;


