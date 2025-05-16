<?php
// preview_cotizacion.php
include_once '../app/config.php';
session_start();

// Verificar datos enviados por POST
// Añadimos la verificación de que 'descuentos' también venga en el POST
if (empty($_POST['cliente_id']) || !isset($_POST['items']) || !isset($_POST['descuentos'])) {
    http_response_code(400);
    echo "<p>Error: Datos de cotización inválidos (faltan cliente, items o descuentos).</p>";
    exit;
}

// Extraer datos del POST
$cliente_id   = (int) $_POST['cliente_id'];
$fecha_cotizacion  = htmlspecialchars($_POST['fecha_cotizacion'] ?? '');
$fecha_validez  = !empty($_POST['fecha_validez']) ? htmlspecialchars($_POST['fecha_validez']) : null;
$estado  = htmlspecialchars($_POST['estado'] ?? '');
$notas  = htmlspecialchars($_POST['notas'] ?? '');
$igvOpciones   = $_POST['igv'] ?? []; // Asegúrate de que esto sea un array asociativo como {'producto': 0.18, 'servicio': 0.18}
$descuentos   = $_POST['descuentos'] ?? []; // Recibimos el array de descuentos fijos (Soles)
$descripcion_cotizacion_productos = htmlspecialchars($_POST['descripcion_cotizacion_productos'] ?? '');
$descripcion_cotizacion_servicios = htmlspecialchars($_POST['descripcion_cotizacion_servicios'] ?? '');
$items   = $_POST['items'] ?? []; // array de items enviados desde JS

// Sanitizar y validar descuentos (asegurar que son números)
$montoDescuentoP = isset($descuentos['producto']) ? (float) $descuentos['producto'] : 0;
$montoDescuentoS = isset($descuentos['servicio']) ? (float) $descuentos['servicio'] : 0;
if ($montoDescuentoP < 0) $montoDescuentoP = 0;
if ($montoDescuentoS < 0) $montoDescuentoS = 0;

// Obtener datos del cliente
try {
    $stmt = $pdo->prepare("SELECT nombre, apellido_paterno, apellido_materno, dni_ruc FROM clientes WHERE id_cliente = :id");
    $stmt->execute([':id' => $cliente_id]);
    $c = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$c) {
        throw new Exception("Cliente no encontrado");
    }

    $cliente_nombre = trim("{$c['nombre']} {$c['apellido_paterno']} {$c['apellido_materno']}");
    $cliente_ruc = $c['dni_ruc'];
} catch (Exception $e) {
    echo "<p>Error al obtener cliente: {$e->getMessage()}</p>";
    exit;
}

// Calcular totales y separar items
$totalProductoNet = 0;
$totalServicioNet = 0;
$productos = [];
$servicios = [];
foreach ($items as $i) {
    // Asegurar que los datos de item son numéricos y válidos
    $cantidad = (float) ($i['cantidad'] ?? 0);
    $precio   = (float) ($i['precioUnitario'] ?? 0);
    if ($cantidad < 0) $cantidad = 0; // Evitar cantidades negativas
    if ($precio < 0) $precio = 0; // Evitar precios negativos

    $sub = $cantidad * $precio;
    if ($i['tipo'] === 'producto') {
        $totalProductoNet += $sub;
        $productos[] = $i; // Guarda el item original, incluyendo nombre, etc.
    } elseif ($i['tipo'] === 'servicio') {
        $totalServicioNet += $sub;
        $servicios[] = $i; // Guarda el item original
    }
}

// --- CÁLCULOS CON DESCUENTO E IGV ---
// Subtotal después de aplicar el descuento fijo (asegurar que no sea negativo)
$subtotalProdDesc = max(0, $totalProductoNet - $montoDescuentoP);
$subtotalServDesc = max(0, $totalServicioNet - $montoDescuentoS);

// Monto de IGV calculado sobre el subtotal después del descuento
$igvRateP = isset($igvOpciones['producto']) ? (float) $igvOpciones['producto'] : 0;
$igvRateS = isset($igvOpciones['servicio']) ? (float) $igvOpciones['servicio'] : 0;

$montoIgvP = $subtotalProdDesc * $igvRateP;
$montoIgvS = $subtotalServDesc * $igvRateS;

// Total de cada sección (subtotal después de descuento + IGV)
$totalProdConIgv = $subtotalProdDesc + $montoIgvP;
$totalServConIgv = $subtotalServDesc + $montoIgvS;

// Total general (suma de los totales de cada sección con IGV)
$totalGeneral = $totalProdConIgv + $totalServConIgv;


// Almacenar en sesión (actualizamos los totales guardados)
$_SESSION['cotizacion'] = [
    'cliente_id'                  => $cliente_id,
    'cliente_nombre'              => $cliente_nombre,
    'cliente_ruc'                 => $cliente_ruc,
    'fecha_cotizacion'            => $fecha_cotizacion,
    'fecha_validez'               => $fecha_validez,
    'estado'                      => $estado,
    'notas'                       => $notas,
    'igv'                         => $igvOpciones, // Guardamos las tasas seleccionadas
    'descuentos'                  => ['producto' => $montoDescuentoP, 'servicio' => $montoDescuentoS], // Guardamos los montos de descuento aplicados
    'descripcion_cotizacion_productos' => $descripcion_cotizacion_productos,
    'descripcion_cotizacion_servicios' => $descripcion_cotizacion_servicios,
    'items'                       => $items, // Guardamos los items originales
    'totales'                     => [ // Guardamos los totales calculados correctamente
        'neto_prod'      => $totalProductoNet,
        'descuento_prod' => $montoDescuentoP,
        'subtotal_desc_prod' => $subtotalProdDesc,
        'igv_prod'       => $montoIgvP,
        'total_prod_igv' => $totalProdConIgv,
        'neto_serv'      => $totalServicioNet,
        'descuento_serv' => $montoDescuentoS,
        'subtotal_desc_serv' => $subtotalServDesc,
        'igv_serv'       => $montoIgvS,
        'total_serv_igv' => $totalServConIgv,
        'total_general'  => $totalGeneral
    ]
];

// Generar HTML de previsualización
?>

<h1 class="text-center">Vista Previa de Cotización</h1>

<div class="preview-header mb-3">
    <p><strong>Cliente:</strong> <?= htmlspecialchars($cliente_nombre) ?> (<?= htmlspecialchars($cliente_ruc) ?>)</p>
    <p><strong>Fecha:</strong> <?= htmlspecialchars($fecha_cotizacion) ?><?php if ($fecha_validez): ?> | <strong>Validez hasta:</strong> <?= htmlspecialchars($fecha_validez) ?><?php endif; ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($estado) ?></p>
    <?php if ($notas): ?><p><strong>Notas:</strong> <?= nl2br(htmlspecialchars($notas)) ?></p><?php endif; ?>
</div>

<?php if (!empty($productos)): ?>
<h5 class="mt-4">Productos</h5>
<table class="table table-sm table-bordered mb-2">
    <thead class="thead-light">
        <tr><th>Nombre</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
    <?php foreach ($productos as $p): $sub = $p['cantidad'] * $p['precioUnitario']; ?>
        <tr>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= number_format((float)$p['cantidad'], 2) ?></td> <?php // Mostrar cantidad con 2 decimales si aplica ?>
            <td>S/ <?= number_format((float)$p['precioUnitario'], 2) ?></td>
            <td>S/ <?= number_format($sub, 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="3" class="text-right">Total Neto Productos:</th><th>S/ <?= number_format($totalProductoNet, 2) ?></th></tr>
        <?php if ($montoDescuentoP > 0): // Mostrar descuento solo si es mayor que 0 ?>
        <tr><th colspan="3" class="text-right">Descuento Productos:</th><th>- S/ <?= number_format($montoDescuentoP, 2) ?></th></tr>
        <tr><th colspan="3" class="text-right">Subtotal Productos (Neto - Descuento):</th><th>S/ <?= number_format($subtotalProdDesc, 2) ?></th></tr>
        <?php endif; ?>
        <tr><th colspan="3" class="text-right"><?= ($igvRateP > 0 ? 'IGV (' . ($igvRateP * 100) . '%)' : 'Sin IGV') ?> Productos:</th><th>S/ <?= number_format($montoIgvP, 2) ?></th></tr>
        <tr><th colspan="3" class="text-right"><strong>Total Productos (con IGV):</strong></th><th><strong>S/ <?= number_format($totalProdConIgv, 2) ?></strong></th></tr>
    </tfoot>
</table>

    <?php if ($descripcion_cotizacion_productos): ?>
    <div class="mb-4">
        <h5>Descripción de Productos</h5>
        <p><?= nl2br($descripcion_cotizacion_productos) ?></p>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (!empty($servicios)): ?>
<h5 class="mt-4">Servicios</h5>
<table class="table table-sm table-bordered mb-2">
    <thead class="thead-light">
        <tr><th>Nombre</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
    <?php foreach ($servicios as $s): $sub = $s['cantidad'] * $s['precioUnitario']; ?>
        <tr>
            <td><?= htmlspecialchars($s['nombre']) ?></td>
            <td><?= number_format((float)$s['cantidad'], 2) ?></td> <?php // Mostrar cantidad con 2 decimales si aplica ?>
            <td>S/ <?= number_format((float)$s['precioUnitario'], 2) ?></td>
            <td>S/ <?= number_format($sub, 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="3" class="text-right">Total Neto Servicios:</th><th>S/ <?= number_format($totalServicioNet, 2) ?></th></tr>
         <?php if ($montoDescuentoS > 0): // Mostrar descuento solo si es mayor que 0 ?>
        <tr><th colspan="3" class="text-right">Descuento Servicios:</th><th>- S/ <?= number_format($montoDescuentoS, 2) ?></th></tr>
        <tr><th colspan="3" class="text-right">Subtotal Servicios (Neto - Descuento):</th><th>S/ <?= number_format($subtotalServDesc, 2) ?></th></tr>
        <?php endif; ?>
        <tr><th colspan="3" class="text-right"><?= ($igvRateS > 0 ? 'IGV (' . ($igvRateS * 100) . '%)' : 'Sin IGV') ?> Servicios:</th><th>S/ <?= number_format($montoIgvS, 2) ?></th></tr>
        <tr><th colspan="3" class="text-right"><strong>Total Servicios (con IGV):</strong></th><th><strong>S/ <?= number_format($totalServConIgv, 2) ?></strong></th></tr>
    </tfoot>
</table>

    <?php if ($descripcion_cotizacion_servicios): ?>
    <div class="mb-4">
        <h5>Descripción de Servicios</h5>
        <p><?= nl2br($descripcion_cotizacion_servicios) ?></p>
    </div>
    <?php endif; ?>
<?php endif; ?>

<div class="text-right mt-4">
    <h3><strong>Total General: S/ <?= number_format($totalGeneral, 2) ?></strong></h3>
</div>