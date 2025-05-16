<?php
try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("
            SELECT 
                CotizacionID,
                Nombre,
                ClienteID,
                FechaCotizacion,
                FechaValidez,
                Estado,
                Notas,
                Subtotal,
                Impuestos,
                Total,
                ImpuestoID
            FROM cotizaciones 
            WHERE CotizacionID = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $cot = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cot) {
            $response['success'] = true;
            $response['data']    = $cot;
        } else {
            $response['success'] = false;
            $response['message'] = "No se encontró la cotización con id: " . htmlspecialchars($id);
        }
    } else {
        $response['success'] = false;
        $response['message'] = "No se ha especificado el id de la cotización";
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = "Error: " . $e->getMessage();
}

// Devolver JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
