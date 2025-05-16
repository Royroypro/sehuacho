<?php


try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT ProductoID, Nombre, Descripcion, PrecioUnitario, UnidadMedida, Codigo, CategoriaID FROM productos WHERE ProductoID = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $response['success'] = true;
            $response['data'] = $producto;
        } else {
            $response['success'] = false;
            $response['message'] = "No se encontr  el producto con id: " . $id;
        }
    } else {
        $response['success'] = false;
        $response['message'] = "No se ha especificado el id del producto";
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = "Error: " . $e->getMessage();
}

