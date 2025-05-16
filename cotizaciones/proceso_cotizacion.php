<?php
// proceso_cotizacion.php
require_once __DIR__ . '/../app/config.php'; // Asegúrate de que $pdo se inicializa aquí
session_start();

// Siempre devolvemos JSON
header('Content-Type: application/json; charset=utf-8');

// Leer raw input
$rawInput = file_get_contents('php://input');
// Intentar decodificar JSON directo
$data = json_decode($rawInput, true);

// Si no fue JSON válido, ver si viene en $_POST['payload']
if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    if (!empty($_POST['payload'])) {
        $data = json_decode($_POST['payload'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error'   => 'JSON inválido en payload: ' . json_last_error_msg()
            ]);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error'   => 'No se recibió payload JSON o el formato es inválido'
        ]);
        exit;
    }
}

// Validar datos mínimos y estructura
$required = ['cliente_id', 'fecha_cotizacion', 'items', 'igv', 'nombre_cotizacion', 'descuentos'];
foreach ($required as $key) {
    if (!isset($data[$key])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => "Falta dato requerido: $key"]);
        exit;
    }
}

// Validaciones más específicas
if (!is_array($data['items']) || empty($data['items'])) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => "El campo 'items' debe ser un array no vacío."]);
    exit;
}

if (!is_array($data['igv'])) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => "El campo 'igv' debe ser un array."]);
    exit;
}

if (!is_array($data['descuentos']) || !isset($data['descuentos']['producto'], $data['descuentos']['servicio'])) {
     http_response_code(422);
    echo json_encode(['success' => false, 'error' => "El campo 'descuentos' debe ser un array con claves 'producto' y 'servicio'."]);
    exit;
}

// Sanitizar y asignar variables
$clienteID      = (int) $data['cliente_id'];
$fechaCotizacion= $data['fecha_cotizacion']; // Considerar validar formato de fecha
$fechaValidez   = !empty($data['fecha_validez']) ? $data['fecha_validez'] : null; // Considerar validar formato de fecha
$notas          = isset($data['notas']) ? htmlspecialchars($data['notas'], ENT_QUOTES, 'UTF-8') : '';
$nombreCot      = htmlspecialchars($data['nombre_cotizacion'], ENT_QUOTES, 'UTF-8');
$estado         = isset($data['estado']) ? htmlspecialchars($data['estado'], ENT_QUOTES, 'UTF-8') : 'Pendiente';

$descProdSection = isset($data['descripcion_cotizacion_productos'])
    ? htmlspecialchars($data['descripcion_cotizacion_productos'], ENT_QUOTES, 'UTF-8')
    : '';
$descServSection = isset($data['descripcion_cotizacion_servicios'])
    ? htmlspecialchars($data['descripcion_cotizacion_servicios'], ENT_QUOTES, 'UTF-8')
    : '';

$descuentoProductoTotal = (float) $data['descuentos']['producto'];
$descuentoServicioTotal = (float) $data['descuentos']['servicio'];
$descuentoTotalGeneral  = $descuentoProductoTotal + $descuentoServicioTotal;

try {
    // --- Fase 1: Calcular subtotales sin descuento para prorrateo y validar items ---
    $subTotalProductosSinDescuento = 0.0;
    $subTotalServiciosSinDescuento = 0.0;

    foreach ($data['items'] as $index => $item) {
        // Validar estructura mínima y tipos de item
        if (!isset($item['tipo'], $item['cantidad'], $item['precioUnitario'], $item['id'])) {
             http_response_code(422);
             echo json_encode(['success' => false, 'error' => "Item en índice $index con datos incompletos."]);
             exit;
        }
        if (!in_array($item['tipo'], ['producto', 'servicio'])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => "Item en índice $index con tipo inválido."]);
            exit;
        }
        if (!is_numeric($item['cantidad']) || $item['cantidad'] <= 0) {
             http_response_code(422);
             echo json_encode(['success' => false, 'error' => "Item en índice $index con cantidad inválida."]);
             exit;
        }
         if (!is_numeric($item['precioUnitario']) || $item['precioUnitario'] < 0) {
             http_response_code(422);
             echo json_encode(['success' => false, 'error' => "Item en índice $index con precio unitario inválido."]);
             exit;
        }
        if (!is_numeric($item['id'])) {
             http_response_code(422);
             echo json_encode(['success' => false, 'error' => "Item en índice $index con ID inválido."]);
             exit;
        }

        // Calcular subtotal por item antes de cualquier descuento de sección
        $subtotalItemSinDescuento = (float)$item['cantidad'] * (float)$item['precioUnitario'];

        // Sumar a los totales de sección sin descuento
        if ($item['tipo'] === 'producto') {
            $subTotalProductosSinDescuento += $subtotalItemSinDescuento;
        } elseif ($item['tipo'] === 'servicio') {
            $subTotalServiciosSinDescuento += $subtotalItemSinDescuento;
        }
    }

    // --- Fase 2: Calcular totales generales para la cabecera (aplicando descuentos de sección) ---

    // Subtotales de sección con descuento aplicado (no menos de 0)
    $subTotalProductosConDescuento = max(0.0, $subTotalProductosSinDescuento - $descuentoProductoTotal);
    $subTotalServiciosConDescuento = max(0.0, $subTotalServiciosSinDescuento - $descuentoServicioTotal);

    // Subtotal general para la cabecera
    $subTotalGeneralHeader = $subTotalProductosConDescuento + $subTotalServiciosConDescuento;

    // Calcular impuestos generales sobre los subtotales con descuento (asumiendo esta lógica fiscal)
    $tasaIgvProducto = isset($data['igv']['producto']) ? (float)$data['igv']['producto'] : 0.0;
    $tasaIgvServicio = isset($data['igv']['servicio']) ? (float)$data['igv']['servicio'] : 0.0;

    $impuestosProductosConDescuento = $subTotalProductosConDescuento * $tasaIgvProducto;
    $impuestosServiciosConDescuento = $subTotalServiciosConDescuento * $tasaIgvServicio;

    // Impuestos generales para la cabecera
    $impuestosGeneralHeader = $impuestosProductosConDescuento + $impuestosServiciosConDescuento;

    // Total general para la cabecera
    $totalGeneralHeader = $subTotalGeneralHeader + $impuestosGeneralHeader;

    // Elegir ImpuestoID para la cabecera: 1 = sin IGV, 2 = con IGV
    $impuestoID = ($impuestosGeneralHeader > 0.001) ? 2 : 1; // Usamos un umbral pequeño

    // --- Fase 3: Iniciar transacción e insertar datos ---

    $pdo->beginTransaction();

    // Insertar cabecera con los totales calculados
    $sqlHeader = "INSERT INTO cotizaciones
        (Nombre, ClienteID, FechaCotizacion, FechaValidez, Estado, Notas,
         Subtotal, Impuestos, Total, ImpuestoID, Descuento)
        VALUES (:nombre_cot, :cliente, :fCot, :fVal, :estado, :notas,
                :sub, :imp, :total, :impuestoID, :descuento_total)"; // Renombrado a descuento_total para claridad
    $stmtHeader = $pdo->prepare($sqlHeader);
    $stmtHeader->execute([
        ':nombre_cot'   => $nombreCot,
        ':cliente'      => $clienteID,
        ':fCot'         => $fechaCotizacion,
        ':fVal'         => $fechaValidez,
        ':estado'       => $estado,
        ':notas'        => $notas,
        ':sub'          => round($subTotalGeneralHeader, 2), // Redondear para decimal(10,2)
        ':imp'          => round($impuestosGeneralHeader, 2), // Redondear
        ':total'        => round($totalGeneralHeader, 2), // Redondear
        ':impuestoID'   => $impuestoID,
        ':descuento_total' => round($descuentoTotalGeneral, 2), // Guardar el descuento total de la cotización
    ]);
    $cotID = $pdo->lastInsertId();

    // Preparar inserción de detalles
    $stmtProd = $pdo->prepare("INSERT INTO detallecotizacion_productos
        (CotizacionID, ProductoID, Cantidad, PrecioUnitario, Descripcion, Descuento, Impuesto, Subtotal)
        VALUES (:cot, :id, :cant, :precio, :desc, :descuento_item, :imp_item, :sub_item)"); // Renombrado placeholders
    $stmtServ = $pdo->prepare("INSERT INTO detallecotizacion_servicios
        (CotizacionID, ServicioID, Cantidad, PrecioUnitario, Descripcion, Descuento, Impuesto, Subtotal)
        VALUES (:cot, :id, :cant, :precio, :desc, :descuento_item, :imp_item, :sub_item)"); // Renombrado placeholders


    // Insertar cada ítem con el descuento prorrateado
    foreach ($data['items'] as $item) {
         // Calcular subtotal e impuesto por item (basado en precio original, para guardar)
         $subtotalItemSinDescuento = (float)$item['cantidad'] * (float)$item['precioUnitario'];
         $tasaIgvItem      = isset($data['igv'][$item['tipo']]) ? (float)$data['igv'][$item['tipo']] : 0.0;
         $impuestoItemSinDescuento = $subtotalItemSinDescuento * $tasaIgvItem;

         // Calcular el descuento prorrateado para este ítem
         $proratedDiscount = 0.0;
         if ($item['tipo'] === 'producto') {
             if ($subTotalProductosSinDescuento > 0) {
                 $proratedDiscount = ($descuentoProductoTotal * ($subtotalItemSinDescuento / $subTotalProductosSinDescuento));
             }
         } elseif ($item['tipo'] === 'servicio') {
              if ($subTotalServiciosSinDescuento > 0) {
                 $proratedDiscount = ($descuentoServicioTotal * ($subtotalItemSinDescuento / $subTotalServiciosSinDescuento));
             }
         }

        // Asegurarse de que el descuento prorrateado no sea negativo (por si los subtotales de sección se volvieron negativos al aplicar el descuento total)
        $proratedDiscount = max(0.0, $proratedDiscount);

        // Limitar el descuento prorrateado al subtotal del item para evitar descuentos mayores al valor del item
        $proratedDiscount = min($proratedDiscount, $subtotalItemSinDescuento);


        if ($item['tipo'] === 'producto') {
            $stmtProd->execute([
                ':cot'          => $cotID,
                ':id'           => (int)$item['id'],
                ':cant'         => (float)$item['cantidad'],
                ':precio'       => (float)$item['precioUnitario'],
                ':desc'         => $descProdSection, // Usando la descripción general de sección
                ':descuento_item'=> round($proratedDiscount, 2), // Guardar el descuento prorrateado por ítem, redondeado a 2 decimales
                ':imp_item'     => round($impuestoItemSinDescuento, 2), // Guardar impuesto del item (basado en precio original)
                ':sub_item'     => round($subtotalItemSinDescuento, 2), // Guardar subtotal del item (basado en precio original)
            ]);
        } elseif ($item['tipo'] === 'servicio') {
            $stmtServ->execute([
                ':cot'          => $cotID,
                ':id'           => (int)$item['id'],
                ':cant'         => (float)$item['cantidad'],
                ':precio'       => (float)$item['precioUnitario'],
                ':desc'         => $descServSection, // Usando la descripción general de sección
                ':descuento_item'=> round($proratedDiscount, 2), // Guardar el descuento prorrateado por ítem, redondeado a 2 decimales
                ':imp_item'     => round($impuestoItemSinDescuento, 2), // Guardar impuesto del item (basado en precio original)
                ':sub_item'     => round($subtotalItemSinDescuento, 2), // Guardar subtotal del item (basado en precio original)
            ]);
        }
    }

    // Confirmar y responder
    $pdo->commit();
    echo json_encode(['success' => true, 'id_cotizacion' => $cotID]);

} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    error_log("Error al procesar cotización: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Ocurrió un error al guardar la cotización. Por favor, intente de nuevo.']);
    // echo json_encode(['success' => false, 'error' => $e->getMessage()]); // Para desarrollo
} catch (PDOException $e) {
     if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    error_log("Error de base de datos al procesar cotización: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Ocurrió un error en la base de datos. Por favor, intente de nuevo.']);
    // echo json_encode(['success' => false, 'error' => "Error DB: " . $e->getMessage()]); // Para desarrollo
}

?>