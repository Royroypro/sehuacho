<?php
include_once("../../config.php");

// Asegurarnos de enviar siempre JSON
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Obtener datos desde $_POST
        $nombre          = $_POST['nombre_producto'] ?? '';
        $descripcion     = $_POST['descripcion']       ?? null;
        $unidad_medida   = $_POST['unidad_medida']     ?? '';
        $precio_unitario = $_POST['precio_unitario']   ?? '';
        $codigo          = $_POST['codigo_producto']   ?? '';
        // Si en el futuro agregas categoría en el form, úsala; por ahora la dejamos nula
        $categoriaID     = $_POST['categoria_id']      ?? null;

        // Validar campos obligatorios
        $faltan = [];
        if (trim($nombre) === '')          $faltan[] = 'Nombre del producto';
        if (trim($unidad_medida) === '')   $faltan[] = 'Unidad de medida';
        if (trim($precio_unitario) === '') $faltan[] = 'Precio unitario';
        if (trim($codigo) === '')          $faltan[] = 'Código';

        if (count($faltan) > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Faltan campos obligatorios: ' . implode(', ', $faltan)
            ]);
            exit;
        }

        // Preparar la inserción
        $stmt = $pdo->prepare("
            INSERT INTO productos
                (Nombre, Descripcion, PrecioUnitario, UnidadMedida, Codigo, CategoriaID, fecha_creacion)
            VALUES
                (:Nombre, :Descripcion, :PrecioUnitario, :UnidadMedida, :Codigo, :CategoriaID, CURRENT_TIMESTAMP)
        ");

        // Enlazar parámetros
        $stmt->bindValue(':Nombre',          $nombre,          PDO::PARAM_STR);
        $stmt->bindValue(':Descripcion',     $descripcion,     PDO::PARAM_STR);
        $stmt->bindValue(':PrecioUnitario',  $precio_unitario, PDO::PARAM_STR);
        $stmt->bindValue(':UnidadMedida',    $unidad_medida,   PDO::PARAM_STR);
        $stmt->bindValue(':Codigo',          $codigo,          PDO::PARAM_STR);
        $stmt->bindValue(':CategoriaID',     $categoriaID,     PDO::PARAM_INT);

        // Ejecutar
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Producto creado correctamente'
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

