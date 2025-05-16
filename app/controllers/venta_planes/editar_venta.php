<?php
include_once('../../config.php');

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Obtener datos del formulario
        $id = $_POST["id"] ?? null;
        $id_cliente = $_POST["id_cliente"] ?? null;
        $id_plan_servicio = $_POST["id_planes_servicios"] ?? null;
        $Ip = $_POST["Ip"] ?? null;
        $Nombre_wifi = $_POST["Nombre_wifi"] ?? null;
        $Contraseña_wifi = $_POST["Contraseña_wifi"] ?? null;
        $Ubicacion = $_POST["Ubicacion"] ?? null;
        $Foto_ubicacion = $_FILES["Foto_ubicacion"] ?? null;
        $Foto_router = $_FILES["Foto_router"] ?? null;
        $Fecha_inicio = $_POST["Fecha_inicio"] ?? null;
        $Fecha_finalizacion = $_POST["Fecha_finalizacion"] ?? null;
        $Estado = $_POST["Estado"] ?? null;

 

        // Validar datos requeridos
        if (!$id || !$id_cliente || !$id_plan_servicio) {
            throw new Exception("ID, ID del cliente y ID del plan de servicio son obligatorios.");
        }

        // Obtener el nombre del cliente
        $stmt = $pdo->prepare("SELECT nombre FROM clientes WHERE id_cliente = :id_cliente");
        $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombre_cliente = $fila["nombre"] ?? null;

        if (!$nombre_cliente) {
            throw new Exception("El cliente con ID $id_cliente no existe.");
        }

        // Procesar las fotos si están presentes
        $nombre_foto_ubicacion = null;
        $nombre_foto_router = null;

        if (!empty($Foto_ubicacion["name"]) && $Foto_ubicacion["error"] == 0) {
            $extension = pathinfo($Foto_ubicacion["name"], PATHINFO_EXTENSION);
            $nombre_foto_ubicacion = $id_cliente . "_" . str_replace(" ", "_", $nombre_cliente) . "_ubicacion." . $extension;
            $ruta_foto_ubicacion = "fotos_ubicacion/" . $nombre_foto_ubicacion;

            if (file_exists($ruta_foto_ubicacion)) {
                unlink($ruta_foto_ubicacion);
            }

            if (!is_dir("fotos_ubicacion")) {
                mkdir("fotos_ubicacion", 0777, true);
            }

            if (!move_uploaded_file($Foto_ubicacion["tmp_name"], $ruta_foto_ubicacion)) {
                throw new Exception("Error al mover el archivo Foto_ubicacion.");
            }
        }

        if (!empty($Foto_router["name"]) && $Foto_router["error"] == 0) {
            $extension = pathinfo($Foto_router["name"], PATHINFO_EXTENSION);
            $nombre_foto_router = $id_cliente . "_" . str_replace(" ", "_", $nombre_cliente) . "_router." . $extension;
            $ruta_foto_router = "fotos_router/" . $nombre_foto_router;

            if (file_exists($ruta_foto_router)) {
                unlink($ruta_foto_router);
            }

            if (!is_dir("fotos_router")) {
                mkdir("fotos_router", 0777, true);
            }

            if (!move_uploaded_file($Foto_router["tmp_name"], $ruta_foto_router)) {
                throw new Exception("Error al mover el archivo Foto_router.");
            }
        }

        // Preparar la consulta SQL para actualizar los datos
        $query = "UPDATE cliente_planes SET 
            id_cliente = ?, 
            id_planes_servicios = ?, 
            Ip = ?, 
            Nombre_wifi = ?, 
            Contraseña_wifi = ?, 
            Ubicacion = ?, 
            Fecha_inicio = ?, 
            Fecha_finalizacion = ?, 
            Estado = ?";

        // Agregar campos de foto solo si no son null
        $params = [
            $id_cliente,
            $id_plan_servicio,
            $Ip,
            $Nombre_wifi,
            $Contraseña_wifi,
            $Ubicacion,
            $Fecha_inicio,
            $Fecha_finalizacion,
            $Estado
        ];

        if ($nombre_foto_ubicacion !== null) {
            $query .= ", Foto_ubicacion = ?";
            $params[] = $nombre_foto_ubicacion;
        }

        if ($nombre_foto_router !== null) {
            $query .= ", Foto_router = ?";
            $params[] = $nombre_foto_router;
        }

        $query .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $response["success"] = true;
        $response["message"] = "Datos guardados exitosamente.";
    } catch (Exception $e) {
        $response["success"] = false;
        $response["message"] = "Error: " . $e->getMessage();
    }

    echo json_encode($response);
} else {
    $response["success"] = false;
    $response["message"] = "Método no permitido";
    echo json_encode($response);
}


