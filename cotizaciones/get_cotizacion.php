<?php
header('Content-Type: application/json; charset=utf-8');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

try {
    require_once '../app/config.php';

    // 1) Cotización principal + tasa IGV
    $stmt = $pdo->prepare("
        SELECT c.CotizacionID          AS id,
               c.Nombre                 AS nombre_cotizacion,
               c.ClienteID              AS cliente_id,
               DATE_FORMAT(c.FechaCotizacion, '%Y-%m-%d') AS fecha_cotizacion,
               DATE_FORMAT(c.FechaValidez,    '%Y-%m-%d') AS fecha_validez,
               c.Estado                 AS estado,
               c.Notas                  AS notas,
               i.Tasa / 100             AS igv_tasa,
               c.Subtotal               AS subtotal,
               c.Impuestos              AS impuestos,
               c.Total                  AS total
        FROM cotizaciones c
        LEFT JOIN impuestos i ON c.ImpuestoID = i.ImpuestoID
        WHERE c.CotizacionID = :id
    ");
    $stmt->execute([':id' => $id]);
    $cot = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cot) {
        echo json_encode(['error' => 'Cotización no encontrada']);
        exit;
    }

    // 2) Detalle de productos
    $stmt = $pdo->prepare("
        SELECT d.DetalleID  AS detalle_id,
               d.ProductoID AS id,
               p.Nombre     AS nombre,
               d.Cantidad,
               d.PrecioUnitario,
               d.Descripcion,
               d.impuesto   AS igv_item,
               d.Descuento  AS descuento_item
        FROM detallecotizacion_productos d
        JOIN productos p ON d.ProductoID = p.ProductoID
        WHERE d.CotizacionID = :id
    ");
    $stmt->execute([':id' => $id]);
    $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3) Detalle de servicios
    $stmt = $pdo->prepare("
        SELECT d.DetalleServicioID AS detalle_id,
               d.ServicioID        AS id,
               s.Nombre            AS nombre,
               d.Cantidad,
               d.PrecioUnitario,
               d.Descripcion,
               d.impuesto          AS igv_item,
               d.Descuento         AS descuento_item
        FROM detallecotizacion_servicios d
        JOIN servicios s ON d.ServicioID = s.ServicioID
        WHERE d.CotizacionID = :id
    ");
    $stmt->execute([':id' => $id]);
    $servs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4) Calcular descuento promedio por sección
    $stmt = $pdo->prepare("
        SELECT IFNULL(SUM(Descuento), 0) AS descuento
        FROM detallecotizacion_productos
        WHERE CotizacionID = :id
    ");
    $stmt->execute([':id' => $id]);
    $descuento_producto = (float) $stmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT IFNULL(SUM(Descuento), 0) AS descuento
        FROM detallecotizacion_servicios
        WHERE CotizacionID = :id
    ");
    $stmt->execute([':id' => $id]);
    $descuento_servicio = (float) $stmt->fetchColumn();

    // 5) Montamos el array de items
    $items = [];
    foreach ($prods as $r) {
        $items[] = [
            'tipo'           => 'producto',
            'detalle_id'     => $r['detalle_id'],
            'id'             => $r['id'],
            'nombre'         => $r['nombre'],
            'cantidad'       => (float)$r['Cantidad'],
            'precioUnitario' => (float)$r['PrecioUnitario'],
            'descripcion'    => $r['Descripcion'],
            'descuento'      => (float)$r['descuento_item'],
            'igv_item'       => (float)$r['igv_item']
        ];
    }
    foreach ($servs as $r) {
        $items[] = [
            'tipo'           => 'servicio',
            'detalle_id'     => $r['detalle_id'],
            'id'             => $r['id'],
            'nombre'         => $r['nombre'],
            'cantidad'       => (float)$r['Cantidad'],
            'precioUnitario' => (float)$r['PrecioUnitario'],
            'descripcion'    => $r['Descripcion'],
            'descuento'      => (float)$r['descuento_item'],
            'igv_item'       => (float)$r['igv_item']
        ];
    }

    // 6) Respondemos todo en JSON
    echo json_encode([
        'id'                    => (int)$cot['id'],
        'cliente_id'            => (int)$cot['cliente_id'],
        'nombre_cotizacion'     => $cot['nombre_cotizacion'],
        'fecha_cotizacion'      => $cot['fecha_cotizacion'],
        'fecha_validez'         => $cot['fecha_validez'],
        'estado'                => $cot['estado'],
        'notas'                 => $cot['notas'],

        // Totales de la cotización
        'subtotal'              => (float)$cot['subtotal'],
        'impuestos'             => (float)$cot['impuestos'],
        'total'                 => (float)$cot['total'],

        // Descuentos promedio por sección
        'descuentos'            => [
            'producto'  => $descuento_producto,
            'servicio'  => $descuento_servicio
        ],

        // IGV por tipo (misma tasa general)
        'igv'                   => [
            'producto' => (float)$cot['igv_tasa'],
            'servicio' => (float)$cot['igv_tasa']
        ],

        // Descripciones generales
        'descripcion_cotizacion_productos' => $prods[0]['Descripcion'] ?? '',
        'descripcion_cotizacion_servicios' => $servs[0]['Descripcion'] ?? '',

        'items'                 => $items
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
