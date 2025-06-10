<?php
include_once("../../config.php");

// Asegurarnos de enviar siempre JSON
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Obtener datos desde $_POST
        $servicioID     = $_POST['ServicioID'] ?? null;
        $nombre         = $_POST['Nombre'] ?? '';
        $descripcion    = $_POST['Descripcion'] ?? '';
        $unidad_medida  = $_POST['UnidadMedida'] ?? '';
        $precio         = $_POST['Precio'] ?? 0.00;
        $codigo         = $_POST['Codigo'] ?? '';

        // Validar campos obligatorios
        $faltan = [];
        if (trim($nombre) === '')          $faltan[] = 'Nombre del servicio';
        if (trim($codigo) === '')          $faltan[] = 'Código';
        if (trim($unidad_medida) === '')   $faltan[] = 'Unidad de medida';
        if (empty($precio) || $precio <= 0) $faltan[] = 'Precio';
        if (!$servicioID)                  $faltan[] = 'ID del servicio';

        if (count($faltan) > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Faltan campos obligatorios: ' . implode(', ', $faltan)
            ]);
            exit;
        }

        // Preparar la actualización dinámica
        $fields = [];
        if (!empty($nombre)) $fields['Nombre'] = $nombre;
        if (!empty($codigo)) $fields['Codigo'] = $codigo;
        if (!empty($descripcion)) $fields['Descripcion'] = $descripcion;
        if (!empty($precio)) $fields['Precio'] = $precio;
        if (!empty($unidad_medida)) $fields['UnidadMedida'] = $unidad_medida;

        if (!empty($fields)) {
            $setString = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($fields)));
            $fields['ServicioID'] = $servicioID;

            // Preparar la consulta
            $stmt = $pdo->prepare("
                UPDATE servicios SET $setString WHERE ServicioID = :ServicioID
            ");

            // Enlazar parámetros
            foreach ($fields as $key => $value) {
                $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            // Ejecutar
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Servicio actualizado correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se actualizó ningún registro o no hubo cambios'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No hay datos para actualizar'
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

