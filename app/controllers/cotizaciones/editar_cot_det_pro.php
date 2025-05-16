<?php
// Handler de depuración: recibe datos y los muestra en JSON sin modificar la BD
header('Content-Type: application/json; charset=utf-8');
// Deshabilitar salida de errores a pantalla, loggear internamente
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$debug = [];
// Marcar inicio
$debug[] = 'Inicio de recepción de datos';

// Leer datos POST
$cotizacionID = filter_input(INPUT_POST, 'CotizacionID', FILTER_VALIDATE_INT);
$detalleIDs    = $_POST['DetalleID']    ?? [];
$productos     = $_POST['ProductoID']   ?? [];
$cantidades     = $_POST['Cantidad']     ?? [];
$precios        = $_POST['PrecioUnitario'] ?? [];

$debug[] = "CotizacionID: " . var_export($cotizacionID, true);

// Recorrer y registrar cada fila
$rawSubtotal = 0;

foreach ($productos as $i => $prodID) {
    $qty   = $cantidades[$i] ?? null;
    $price = null;
    if ($prodID) {
        $stmt = $pdo->prepare("SELECT PrecioUnitario FROM productos WHERE ProductoID = :id");
        $stmt->execute(['id' => $prodID]);
        $price = $stmt->fetchColumn();
    }
    $sub   = $qty * $price;
    $rawSubtotal += $sub;
    $detID = $detalleIDs[$i] ?? null;
    $debug[] = sprintf(
        "Fila %d → DetalleID=%s, ProductoID=%s, Cantidad=%s, PrecioUnitario=%s, Subtotal=%s",
        $i, var_export($detID, true), var_export($prodID, true), var_export($qty, true), var_export($price, true), var_export($sub, true)
    );
}

// Devolver JSON con los datos recibidos y trazas de depuración
echo json_encode([
    'success'  => true,
    'received' => [
        'CotizacionID'    => $cotizacionID,
        'raw_subtotal'    => $rawSubtotal,
        'DetalleID'       => $detalleIDs,
        'ProductoID'      => $productos,
        'Cantidad'        => $cantidades,
        'PrecioUnitario'  => $precios,
        'Subtotal'        => $rawSubtotal,
    ],
    'debug'    => $debug,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;

