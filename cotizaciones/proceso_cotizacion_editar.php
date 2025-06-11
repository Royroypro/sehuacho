<?php
// proceso_cotizacion.php
require_once __DIR__ . '/../app/config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

// Leer y decodificar JSON
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    if (!empty($_POST['payload'])) {
        $data = json_decode($_POST['payload'], true);
    }
}
if (!is_array($data)) {
    http_response_code(400);
    exit(json_encode(['success'=>false,'error'=>'JSON inválido']));
}

// Validar campos requeridos
$required = ['cliente_id','fecha_cotizacion','items','igv','nombre_cotizacion','descuentos'];
foreach ($required as $key) {
    if (!isset($data[$key])) {
        http_response_code(422);
        exit(json_encode(['success'=>false,'error'=>"Falta dato: $key"]));
    }
}

// Sanitizar y asignar
$cotID       = isset($data['id']) && is_numeric($data['id']) ? (int)$data['id'] : 0;
$clienteID   = (int)$data['cliente_id'];
$fechaCot    = $data['fecha_cotizacion'];
$fechaVal    = !empty($data['fecha_validez']) ? $data['fecha_validez'] : null;
$nombreCot   = htmlspecialchars($data['nombre_cotizacion'],ENT_QUOTES,'UTF-8');
$notas       = isset($data['notas']) ? htmlspecialchars($data['notas'],ENT_QUOTES,'UTF-8') : '';
$estado      = isset($data['estado']) ? htmlspecialchars($data['estado'],ENT_QUOTES,'UTF-8') : 'Pendiente';
$descProdSec = isset($data['descripcion_cotizacion_productos'])
               ? htmlspecialchars($data['descripcion_cotizacion_productos'],ENT_QUOTES,'UTF-8') : '';
$descServSec = isset($data['descripcion_cotizacion_servicios'])
               ? htmlspecialchars($data['descripcion_cotizacion_servicios'],ENT_QUOTES,'UTF-8') : '';
$descProdTot = (float)$data['descuentos']['producto'];
$descServTot = (float)$data['descuentos']['servicio'];
$descTotGen  = $descProdTot + $descServTot;

// Calcular subtotales y totales (igual que antes)...
$subProdSin = $subServSin = 0;
foreach ($data['items'] as $idx => $item) {
    // ...validaciones...
    $st = $item['cantidad'] * $item['precioUnitario'];
    if ($item['tipo']==='producto') $subProdSin += $st;
    else                          $subServSin += $st;
}
$stProdCon = max(0,$subProdSin - $descProdTot);
$stServCon = max(0,$subServSin - $descServTot);
$stGenHdr  = $stProdCon + $stServCon;
$tasaIgvP  = (float)$data['igv']['producto'];
$tasaIgvS  = (float)$data['igv']['servicio'];
$impProd   = $stProdCon * $tasaIgvP;
$impServ   = $stServCon * $tasaIgvS;
$impGenHdr = $impProd + $impServ;
$totGenHdr = $stGenHdr + $impGenHdr;
$impuestoID = $impGenHdr>0.001? 2:1;


try {
    $pdo->beginTransaction();

    if (isset($cotID) && $cotID > 0) {
        // --- ACTUALIZAR CABECERA EXISTENTE ---
        $codigo = sha1($cotID);

        $sql = "UPDATE cotizaciones SET
                  Nombre          = :nombre,
                  ClienteID       = :cliente,
                  FechaCotizacion = :fCot,
                  FechaValidez    = :fVal,
                  Estado          = :estado,
                  Notas           = :notas,
                  Subtotal        = :sub,
                  Impuestos       = :imp,
                  Total           = :total,
                  ImpuestoID      = :impuestoID,
                  Descuento       = :descuento_total,
                  codigo          = :codigo
                WHERE CotizacionID = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'          => $nombreCot,
            ':cliente'         => $clienteID,
            ':fCot'            => $fechaCot,
            ':fVal'            => $fechaVal,
            ':estado'          => $estado,
            ':notas'           => $notas,
            ':sub'             => round($stGenHdr, 2),
            ':imp'             => round($impGenHdr, 2),
            ':total'           => round($totGenHdr, 2),
            ':impuestoID'      => $impuestoID,
            ':descuento_total' => round($descTotGen, 2),
            ':codigo'          => $codigo,
            ':id'              => $cotID
        ]);

        // Borrar detalles antiguos
        $pdo->prepare("DELETE FROM detallecotizacion_productos WHERE CotizacionID = :id")
            ->execute([':id' => $cotID]);
        $pdo->prepare("DELETE FROM detallecotizacion_servicios WHERE CotizacionID = :id")
            ->execute([':id' => $cotID]);

    } else {
        // --- INSERTAR NUEVA CABECERA SIN CÓDIGO ---
        $sql = "INSERT INTO cotizaciones
                 (Nombre, ClienteID, FechaCotizacion, FechaValidez, Estado, Notas,
                  Subtotal, Impuestos, Total, ImpuestoID, Descuento)
                VALUES
                 (:nombre, :cliente, :fCot, :fVal, :estado, :notas,
                  :sub, :imp, :total, :impuestoID, :descuento_total)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'          => $nombreCot,
            ':cliente'         => $clienteID,
            ':fCot'            => $fechaCot,
            ':fVal'            => $fechaVal,
            ':estado'          => $estado,
            ':notas'           => $notas,
            ':sub'             => round($stGenHdr, 2),
            ':imp'             => round($impGenHdr, 2),
            ':total'           => round($totGenHdr, 2),
            ':impuestoID'      => $impuestoID,
            ':descuento_total' => round($descTotGen, 2)
        ]);

        // 1) Obtener el nuevo ID generado
        $cotID = $pdo->lastInsertId();
        // 2) Calcular su hash
        $codigo = sha1($cotID);
        // 3) Actualizar solo la columna codigo (en minúsculas)
        $pdo->prepare("UPDATE cotizaciones
                       SET codigo = :codigo
                       WHERE CotizacionID = :id")
            ->execute([
                ':codigo' => $codigo,
                ':id'     => $cotID
            ]);
    }

    // --- PREPARAR INSERCIÓN DE DETALLES ---
    $insProd = $pdo->prepare("
        INSERT INTO detallecotizacion_productos
          (CotizacionID, ProductoID, Cantidad, PrecioUnitario, Descripcion,
           Descuento, Impuesto, Subtotal)
        VALUES (:cot, :id, :cant, :precio, :desc, :descuento_item, :imp_item, :sub_item)
    ");
    $insServ = $pdo->prepare("
        INSERT INTO detallecotizacion_servicios
          (CotizacionID, ServicioID, Cantidad, PrecioUnitario, Descripcion,
           Descuento, Impuesto, Subtotal)
        VALUES (:cot, :id, :cant, :precio, :desc, :descuento_item, :imp_item, :sub_item)
    ");

    foreach ($data['items'] as $item) {
        $stItem   = $item['cantidad'] * $item['precioUnitario'];
        $tasaIgv  = (float)$data['igv'][$item['tipo']];
        $impItem  = $stItem * $tasaIgv;
        // Prorrateo de descuento
        $prorrateo = ($item['tipo'] === 'producto')
            ? ($subProdSin > 0 ? $descProdTot * ($stItem / $subProdSin) : 0)
            : ($subServSin > 0 ? $descServTot * ($stItem / $subServSin) : 0);
        $prorrateo = max(0, min($prorrateo, $stItem));

        $params = [
            ':cot'            => $cotID,
            ':id'             => (int)$item['id'],
            ':cant'           => (float)$item['cantidad'],
            ':precio'         => (float)$item['precioUnitario'],
            ':desc'           => $item['tipo'] === 'producto' ? $descProdSec : $descServSec,
            ':descuento_item' => round($prorrateo, 2),
            ':imp_item'       => round($impItem, 2),
            ':sub_item'       => round($stItem, 2),
        ];

        if ($item['tipo'] === 'producto') {
            $insProd->execute($params);
        } else {
            $insServ->execute($params);
        }
    }

    $pdo->commit();

    echo json_encode([
        'success'         => true,
        'id_cotizacion'   => $cotID,
        'codigo_hasheado' => $codigo
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}
