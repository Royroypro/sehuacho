<?php
include_once("../../config.php");
header('Content-Type: application/json');

$response = [];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener los datos JSON enviados
        $datos = json_decode(file_get_contents('php://input'), true);
        
        // Validar que los datos necesarios estén presentes
        if (!isset($datos["id_cliente"])) {
            throw new Exception("ID del cliente es requerido.");
        }

        // Asignar variables
        $id_cliente = $datos["id_cliente"];
        $nombre = $datos["nombre"];
        $apellido_paterno = $datos["apellido_paterno"];
        $apellido_materno = $datos["apellido_materno"];
        $tipo_documento = $datos["tipo_documento"];
        $dni_ruc = $datos["dni_ruc"];
        $celular = $datos["celular"];
        $email = $datos["email"];
        $direccion = $datos["direccion"];
        $referencia = $datos["referencia"];
        $estado = $datos["estado"];

        // Preparar la consulta SQL
        $stmt = $pdo->prepare("UPDATE clientes SET nombre = :nombre, apellido_paterno = :apellido_paterno, apellido_materno = :apellido_materno, tipo_documento = :tipo_documento, dni_ruc = :dni_ruc, celular = :celular, email = :email, direccion = :direccion, referencia = :referencia, estado = :estado WHERE id_cliente = :id_cliente");
        
        // Vincular parámetros
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido_paterno', $apellido_paterno);
        $stmt->bindParam(':apellido_materno', $apellido_materno);
        $stmt->bindParam(':tipo_documento', $tipo_documento);
        $stmt->bindParam(':dni_ruc', $dni_ruc);
        $stmt->bindParam(':celular', $celular);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':referencia', $referencia);
        $stmt->bindParam(':estado', $estado);
        
        // Ejecutar la consulta
        $stmt->execute();

        // Respuesta de éxito
        $response['success'] = true;
        $response['message'] = "Guardado correctamente";
    } catch (Exception $e) {
        // Respuesta de error
        $response['success'] = false;
        $response['message'] = "Error: " . $e->getMessage();
    }
} else {
    // Respuesta para método no permitido
    $response['success'] = false;
    $response['message'] = "Método no permitido";
}

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;

