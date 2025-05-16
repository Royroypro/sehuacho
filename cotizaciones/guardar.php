<?php

include_once '../app/config.php'; // aquí incluyes tu conexión PDO ($pdo)
/**
 * guardar.php
 * Recibe datos de productos desde el modal y actualiza la cotización.
 */

require_once '../app/config.php'; // aquí incluyes tu conexión PDO ($pdo)



// Capturar POST
$cotizacionID    = $_POST['CotizacionID'] ?? null;
$productos       = $_POST['ProductoID']      ?? [];
$cantidades      = $_POST['Cantidad']        ?? [];
$precios         = $_POST['PrecioUnitario']  ?? [];

if (!$cotizacionID || count($productos) === 0) {
    die(json_encode([ 'success' => false, 'message' => 'Datos incompletos.' ]));
}

// Calcular subtotales e impuestos
$subtotales = array_map(function($cantidad, $precio) {
    return $cantidad * $precio;
}, $cantidades, $precios);

$impuestos = array_map(function($subtotal) {
    return $subtotal * 0.18;
}, $subtotales);

// Consultar subtotal e impuesto de los servicios asociados a la cotización
$stmt = $pdo->prepare("
    SELECT SUM(Subtotal) AS Subtotal, SUM(impuesto) AS Impuesto
    FROM detallecotizacion_servicios 
    WHERE CotizacionID = :id
");
$stmt->bindParam(':id', $cotizacionID, PDO::PARAM_INT);
$stmt->execute();
$servicios = $stmt->fetch(PDO::FETCH_ASSOC);


// Mostrar lo que se recibió
echo json_encode([
    'success' => true,
    'message' => 'Datos recibidos con éxito.',
    'datos' => [
        'CotizacionID'    => $cotizacionID,
        'Productos'       => $productos,
        'Cantidades'      => $cantidades,
        'Precios'         => $precios,
        'Subtotales'      => $subtotales,
        'Impuestos'       => $impuestos,
        'Total'           => array_sum($subtotales) + array_sum($impuestos),
        'Servicios' => [
            'Subtotal' => $servicios['Subtotal'],
            'Impuesto' => $servicios['Impuesto']
        ]
    ]
]);




