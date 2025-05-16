<?php
include_once("../../config.php");

// Asegurarnos de enviar siempre JSON
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Obtener datos desde $_POST
        $productoID     = $_POST['ProductoID'] ?? null;
        $nombre         = $_POST['Nombre'] ?? '';
        $descripcion    = $_POST['Descripcion'] ?? null;
        $unidad_medida  = $_POST['UnidadMedida'] ?? '';
        $precio_unitario= $_POST['PrecioUnitario'] ?? '';
        $codigo         = $_POST['Codigo'] ?? '';
        $categoriaID    = $_POST['CategoriaID'] ?? null;

        // Validar campos obligatorios
        $faltan = [];
        if (trim($nombre) === '')          $faltan[] = 'Nombre del producto';
        if (trim($unidad_medida) === '')   $faltan[] = 'Unidad de medida';
        if (trim($precio_unitario) === '') $faltan[] = 'Precio unitario';
        if (trim($codigo) === '')          $faltan[] = 'Código';
        if (!$productoID)                  $faltan[] = 'ID del producto';

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
        if (!is_null($descripcion)) $fields['Descripcion'] = $descripcion;
        if (!empty($precio_unitario)) $fields['PrecioUnitario'] = $precio_unitario;
        if (!empty($unidad_medida)) $fields['UnidadMedida'] = $unidad_medida;
        if (!empty($codigo)) $fields['Codigo'] = $codigo;
        if (!is_null($categoriaID)) $fields['CategoriaID'] = $categoriaID;

        if (!empty($fields)) {
            $setString = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($fields)));
            $fields['ProductoID'] = $productoID;

            // Preparar la consulta
            $stmt = $pdo->prepare("
                UPDATE productos SET $setString WHERE ProductoID = :ProductoID
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
                    'message' => 'Producto actualizado correctamente'
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

