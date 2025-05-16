<?php
include_once('../../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener datos del formulario
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
        $Estado = 1;

        // Obtener el nombre del cliente
        $stmt = $pdo->prepare("SELECT nombre FROM clientes WHERE id_cliente = :id_cliente");
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombre_cliente = $fila["nombre"] ?? null;
        echo "Nombre Cliente: $nombre_cliente\n"; // Depuración
        $nombre_foto_ubicacion = null;
        $nombre_foto_router = null;
        // Procesar fotos de ubicación y router solo si hay
        if ($Foto_ubicacion["name"]) {
            $extension = pathinfo($Foto_ubicacion["name"], PATHINFO_EXTENSION);
            $nombre_foto_ubicacion = $id_cliente . "_" . str_replace(" ", "_", $nombre_cliente) . "." . $extension;
            $ruta_foto_ubicacion = "fotos_ubicacion/" . $nombre_foto_ubicacion;
            if (move_uploaded_file($Foto_ubicacion["tmp_name"], $ruta_foto_ubicacion)) {
                $Foto_ubicacion["name"] = $nombre_foto_ubicacion;
            } else {
                throw new Exception("Error al mover el archivo Foto_ubicacion");
            }
        }

        if ($Foto_router["name"]) {
            $extension = pathinfo($Foto_router["name"], PATHINFO_EXTENSION);
            $nombre_foto_router = $id_cliente . "_" . str_replace(" ", "_", $nombre_cliente) . "." . $extension;
            $ruta_foto_router = "fotos_router/" . $nombre_foto_router;
            if (move_uploaded_file($Foto_router["tmp_name"], $ruta_foto_router)) {
                $Foto_router["name"] = $nombre_foto_router;
            } else {
                throw new Exception("Error al mover el archivo Foto_router");
            }
        }

       

        // Preparar la consulta SQL con PDO
        $query = "INSERT INTO cliente_planes 
            (id_cliente, id_planes_servicios, Ip, Nombre_wifi, Contraseña_wifi, Ubicacion, Foto_ubicacion, Foto_router, Fecha_inicio, Fecha_finalizacion, Estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Ejecutar la consulta con parámetros
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $id_cliente, 
            $id_plan_servicio, 
            $Ip, 
            $Nombre_wifi, 
            $Contraseña_wifi, 
            $Ubicacion, 
            $nombre_foto_ubicacion, 
            $nombre_foto_router, 
            $Fecha_inicio, 
            $Fecha_finalizacion, 
            $Estado
        ]);

        echo "Datos guardados exitosamente.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Método no permitido";
}
?>
