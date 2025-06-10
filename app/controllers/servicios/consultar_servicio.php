<?php

try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT ServicioID, Nombre, codigo, Descripcion, Precio, UnidadMedida FROM servicios WHERE ServicioID = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($servicio) {
            $response['success'] = true;
            $response['data'] = $servicio;
        } else {
            $response['success'] = false;
            $response['message'] = "No se encontró el servicio con id: " . $id;
        }
    } else {
        $response['success'] = false;
        $response['message'] = "No se ha especificado el id del servicio";
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = "Error: " . $e->getMessage();
}

