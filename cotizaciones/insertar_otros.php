<?php
// insertar_otros.php
include_once '../app/config.php';
header('Content-Type: application/json; charset=utf-8');

// --- Debug inicial ---
error_log('--- insertar_otros.php Debug Start ---');
error_log('$_POST: ' . print_r($_POST, true));

$tipo        = $_POST['tipo']        ?? '';
$nombre      = trim($_POST['nombre'] ?? '');
$precio      = isset($_POST['precio'])   ? (float)$_POST['precio']   : 0;
$descripcion = trim($_POST['descripcion'] ?? '');

// --- Validación ---
$errores = [];
if (!in_array($tipo, ['producto', 'servicio'])) {
    $errores[] = "tipo inválido ({$tipo})";
}
if (!$nombre) {
    $errores[] = "nombre vacío";
}
if ($precio <= 0) {
    $errores[] = "precio inválido ({$precio})";
}

if (!empty($errores)) {
    $msg = implode('; ', $errores);
    error_log("Validación fallida: $msg");
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => "Parámetros inválidos: $msg"]);
    exit;
}

try {
    if ($tipo === 'producto') {
        // Insertamos en productos usando 'NIU' como unidad por defecto
        $sql = "INSERT INTO productos
                  (Nombre, Descripcion, PrecioUnitario, UnidadMedida, Codigo, CategoriaID, estado, fecha_creacion, fecha_actualizacion)
                VALUES
                  (:nombre, :descripcion, :precio, 'ZZ', NULL, NULL, '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $params = [
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':precio'      => $precio
        ];
    } else {
        // Insertamos en servicios
        $sql = "INSERT INTO servicios
                  (Nombre, Codigo, Descripcion, Precio, estado, fecha_creacion, fecha_actualizacion)
                VALUES
                  (:nombre, '', :descripcion, :precio, '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $params = [
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':precio'      => $precio
        ];
    }

    error_log("SQL: $sql");
    error_log('Params: ' . print_r($params, true));

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $nuevoId = (int)$pdo->lastInsertId();
    error_log("Inserción OK, nuevo ID: $nuevoId");
    error_log('--- insertar_otros.php Debug End ---');

    echo json_encode(['success' => true, 'id' => $nuevoId]);

} catch (PDOException $e) {
    $err = $e->getMessage();
    error_log("Error de BD: $err");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => "BD error: $err"]);
}
