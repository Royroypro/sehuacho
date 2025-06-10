<?php
include_once("../../config.php");

// Asegurarnos de enviar siempre JSON
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Obtener datos desde $_POST
        $nombre_servicio    = $_POST['nombre_servicio'] ?? '';
        $descripcion        = $_POST['descripcion'] ?? '';
        $unidad_medida      = $_POST['unidad_medida'] ?? '';
        $precio_unitario    = $_POST['precio_unitario'] ?? 0.00;
        $codigo_servicio    = $_POST['codigo_servicio'] ?? '';

        // Validar campos obligatorios
        $faltan = [];
        if (trim($nombre_servicio) === '')          $faltan[] = 'Nombre del servicio';
        if (trim($codigo_servicio) === '')          $faltan[] = 'Código';
        if (empty($precio_unitario) || $precio_unitario <= 0) $faltan[] = 'Precio unitario';

        if (count($faltan) > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Faltan campos obligatorios: ' . implode(', ', $faltan)
            ]);
            exit;
        }

        // Preparar la inserción
        $stmt = $pdo->prepare("
            INSERT INTO servicios
                (Nombre, codigo, Descripcion, Precio, UnidadMedida, estado, fecha_creacion, fecha_actualizacion)
            VALUES
                (:Nombre, :codigo, :Descripcion, :Precio, :UnidadMedida, :estado, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");

        // Enlazar parámetros
        $stmt->bindValue(':Nombre',          $nombre_servicio,          PDO::PARAM_STR);
        $stmt->bindValue(':codigo',          $codigo_servicio,          PDO::PARAM_STR);
        $stmt->bindValue(':Descripcion',     $descripcion,             PDO::PARAM_STR);
        $stmt->bindValue(':Precio',          $precio_unitario,         PDO::PARAM_STR);
        $stmt->bindValue(':UnidadMedida',    $unidad_medida,           PDO::PARAM_STR);
        $stmt->bindValue(':estado',          1,                        PDO::PARAM_INT);

        // Ejecutar
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Servicio creado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se insertó ningún registro'
            ]);
        }
    } catch (Exception $e) {
        // Capturar error y devolver mensaje
        echo json_encode([
            'success' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Si no es POST, devolver método no permitido
http_response_code(405);
echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);

