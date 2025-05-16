<?php


try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT nombre, apellido_paterno, apellido_materno, dni_ruc, tipo_documento, celular, email, direccion, referencia, estado FROM clientes WHERE id_cliente = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            $response['success'] = true;
            $response['data'] = $cliente;
        } else {
            $response['success'] = false;
            $response['message'] = "No se encontr  el cliente con id: " . $id;
        }
    } else {
        $response['success'] = false;
        $response['message'] = "No se ha especificado el id del cliente";
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = "Error al consultar el cliente: " . $e->getMessage();
}

