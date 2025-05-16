<?php
header('Content-Type: application/json');
require_once '../app/config.php';
include_once '../layout/sesion.php';

// Obtener datos del POST
$id_recibo = $_POST['id_recibo'] ?? null;
$estado = $_POST['estado'] ?? null;

error_log("ID Recibo: $id_recibo, Estado: $estado");

if ($id_recibo && $estado) {
    try {
        // Preparar la consulta
        $stmt = $pdo->prepare("UPDATE recibos SET estado = :estado WHERE id_recibo = :id_recibo");
        
        // Ejecutar la consulta
        if ($stmt->execute([':estado' => $estado, ':id_recibo' => $id_recibo])) {
            echo json_encode(['success' => true, 'estadoactualizado' => $estado]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
}
?>
