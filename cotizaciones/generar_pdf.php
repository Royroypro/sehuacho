<?php
// 0. Autoload y config
require_once __DIR__ . '/../facturas/vendor/autoload.php';
include_once __DIR__ . '/../app/config.php';  // define $pdo (PDO)


date_default_timezone_set('America/Lima');

// --- LOGO BASE64 ---
$logoPath = __DIR__ . '/logo.png';
$logoSrc = '';
if (file_exists($logoPath)) {
    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
    if (in_array(strtolower($logoType), ['png', 'jpg', 'jpeg'])) {
        $logoData = file_get_contents($logoPath);
        if ($logoData !== false) {
            $logoSrc = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        } else {
            error_log("Error: No se pudo leer el archivo del logo: {$logoPath}");
        }
    } else {
        error_log("Error: Tipo de logo no soportado: {$logoType}");
    }
} else {
    error_log("Error: Logo no encontrado: {$logoPath}");
}

// 1. Validar ID

$cot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$cot_id) die('ID de cotización inválido');
// 2. Datos empresa
$sqlEmp = "SELECT e.ruc, e.razon_social, e.nombre_comercial, d.departamento, d.provincia, d.distrito, d.direccion AS dir_completa
           FROM empresas e
           LEFT JOIN direccion d ON e.id_direccion = d.id
           WHERE e.id = 1";
$stmt = $pdo->prepare($sqlEmp);
$stmt->execute();
$empresa = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// 3. Cabecera cotización
$sqlCot = "SELECT c.CotizacionID, c.Nombre, c.FechaCotizacion, c.FechaValidez, c.Estado, c.Notas,
                  c.Subtotal, c.Impuestos, c.Total, c.ImpuestoID,
                  cli.nombre, cli.apellido_paterno, cli.apellido_materno,
                  cli.dni_ruc, cli.tipo_documento, cli.direccion AS cli_direccion,
                  c.Total
           FROM cotizaciones c
           JOIN clientes cli ON c.ClienteID = cli.id_cliente
           WHERE c.CotizacionID = :id";
$stmt = $pdo->prepare($sqlCot);
$stmt->execute([':id' => $cot_id]);
$cot = $stmt->fetch(PDO::FETCH_ASSOC) ?: die('Cotización no encontrada');

// 4. Detalle Productos
$sqlDetP = "SELECT d.Cantidad, d.PrecioUnitario, d.Descuento, d.Descripcion, d.Subtotal, d.Impuesto, p.Nombre AS prod_nombre, d.Descuento AS descuento_item
            FROM detallecotizacion_productos d
            JOIN productos p ON d.ProductoID = p.ProductoID
            WHERE d.CotizacionID = :id";
$stmt = $pdo->prepare($sqlDetP);
$stmt->execute([':id' => $cot_id]);
$detProd = $stmt->fetchAll(PDO::FETCH_ASSOC);

$descuentoProducto = 0;
foreach ($detProd as $item) {
    $descuentoProducto += $item['descuento_item'];
}

$totalProductos = 0;
$igvProductos = 0;
foreach ($detProd as $item) {
    $totalProductos += $item['Subtotal'];
    $igvProductos += $item['Impuesto'];
}
$totalProductosConIGV = $totalProductos + $igvProductos;

// 5. Detalle Servicios
$sqlDetS = "SELECT d.Cantidad, d.PrecioUnitario, d.Descuento, d.Subtotal, d.Impuesto, d.Descuento AS descuento_item, s.Nombre AS serv_nombre
            FROM detallecotizacion_servicios d
            JOIN servicios s ON d.ServicioID = s.ServicioID
            WHERE d.CotizacionID = :id";
$stmt = $pdo->prepare($sqlDetS);
$stmt->execute([':id' => $cot_id]);
$detServ = $stmt->fetchAll(PDO::FETCH_ASSOC);

$descuentoServicio = 0;
$totalServicios = 0;
$igvServicios = 0;
foreach ($detServ as $item) {
    $descuentoServicio += $item['descuento_item'];
    $totalServicios += $item['Subtotal'];
    $igvServicios += $item['Impuesto'];
}

$totalServiciosConIGV = $totalServicios + $igvServicios;

// Total general
$totalGeneral = $totalProductosConIGV + $totalServiciosConIGV;

// 6. Fecha formateada
$fmt = new IntlDateFormatter('es_PE', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'America/Lima');
$fechaHoy = $fmt->format(new DateTime());

include_once __DIR__ . '/generar_qr.php'; // Funciones de utilidades




// Terminar el script para evitar que se envíe más contenido
// 7. Generar HTML
ob_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
  :root {
    --primary: #005691;
    --secondary: #0073c8;
    --green: rgb(83, 207, 112);
    --text-dark: #000;
    --footer-border: #cccccc;
    --footer-text: #555555;
  }
  body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    margin: 0px;
    color: var(--text-dark);
  }
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: -18px;
  }
  .header img {
    max-height: 120px;
    max-width: 190px;
    width: auto;
    height: auto;
    margin: 0;
  }
  .header .info {
    text-align: right;
    font-size: 12px;
    margin-top: -100px;
  }
  h2, h3 {
    margin: 3px 0; /* Reduced top margin */
    text-align: center;
    color: var(--primary);
    margin-top: -20px;
  }
  h3 {
    color: var(--secondary);
  }
  h4 {
    margin: 12px 0 5px 0; /* Reduced top margin */
    color: var(--secondary);
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
  }
  th, td {
    border: 1px solid #000;
    padding: 5px;
  }
  th {
    background-color: var(--green);
    color: #000;
    text-align: left;
  }
  .right {
    text-align: right;
  }
  .totals {
    text-align: right;
    margin-top: 5px;
  }
  .totals p {
    margin: 2px 0;
  }
  .notas {
    font-size: 11px;
    margin-top: 15px;
  }
  .footer {
    border-top: 1px solid var(--footer-border);
    margin-top: 15px;
    padding-top: 10px;
    font-size: 10px;
    color: var(--footer-text);
    text-align: center;
  }
</style>
</head>
<body>
  <div class="header">
    <?php if ($logoSrc): ?>

     <img src="data:image/png;base64,<?= $qrBase64 ?>" alt="logo"/>
       <BR></BR>

    <?php else: ?>
      <p style="color:red;">Logo no disponible</p>
    <?php endif; ?>
    <div class="info"><?= htmlspecialchars($empresa['distrito'] ?? 'Huacho') ?>, <?= htmlspecialchars($fechaHoy) ?></div>
  </div>
<br>
  <h3><?= htmlspecialchars($cot['Nombre']) ?></h3>
  
  <p style="font-size: 12px; margin-bottom: 10px; text-align: center;">SERVICIOS DE SEGURIDAD ELECTRONICA, GASFITERIA Y ELECTRICIDAD</p>
  

  <p><strong>Sres:</strong> <?= htmlspecialchars("{$cot['nombre']} {$cot['apellido_paterno']} {$cot['apellido_materno']}") ?></p>
  <?php if (!empty($cot['dni_ruc'])): ?>
    <p><strong><?= htmlspecialchars($cot['tipo_documento']) ?>: <?= htmlspecialchars($cot['dni_ruc']) ?></strong></p>
  <?php endif; ?>
  <p><strong>Dirección:</strong> <?= htmlspecialchars($cot['cli_direccion'] ?? '') ?></p>

  <?php if (count($detProd)): ?>
    <h4>Detalle de Productos</h4>
    <table>
      <thead>
        <tr>
          <th>#</th><th>Descripción</th><th>Cant.</th><th>P.Unitario</th><th>Total</th>
        </tr>
      </thead>
      <tbody>
      <?php 
        $i = 1;
        $totalProductos = 0;
        foreach($detProd as $d):
          $subtotal = $d['PrecioUnitario'] * $d['Cantidad'];
          $totalProductos += $subtotal;
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($d['prod_nombre']) ?></td>
          <td class="right"><?= $d['Cantidad'] ?></td>
          <td class="right"><?= number_format($d['PrecioUnitario'],2) ?></td>
          <td class="right"><?= number_format($subtotal,2) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <?php
      // Calculamos monto de descuento y neto productos
      $montoDescProd = $descuentoProducto; // Descuento en soles directamente
      $netoProductos = $totalProductos - $montoDescProd;
      $igvProductos  = $netoProductos * ($cot['ImpuestoID'] == 1 ? 0 : 0.18);
      $totalConIGVProd = ($netoProductos > 0) ? $netoProductos + $igvProductos : 0;
    ?>

    <div class="totals">
      <p><strong>Subtotal Productos:</strong> S/. <?= number_format($totalProductos,2) ?></p>
      <p>
        <strong>Descuento Productos :</strong>
        S/. <?= number_format($montoDescProd,2) ?>
      </p>
      <p><strong>Neto Productos:</strong> S/. <?= number_format($netoProductos,2) ?></p>
      <p>
        <strong>IGV Productos (<?= $cot['ImpuestoID'] == 1 ? '0' : '18' ?>%):</strong>
        S/. <?= number_format($igvProductos,2) ?>
      </p>
      <p><strong>Total Productos:</strong> S/. <?= number_format($totalConIGVProd,2) ?></p>
    </div>

    <p><?= nl2br(htmlspecialchars($detProd[0]['Descripcion'])) ?></p>
    <?php if ($igvProductos == 0): ?>
      <p style="color:red; font-weight:bold; margin-top:5px;">
        Nota: La cotización de productos NO incluye IGV
      </p>
    <?php endif; ?>
  <?php endif; ?>


  <?php if (count($detServ)): ?>
    <h4>Detalle de Servicios</h4>
    <table>
      <thead>
        <tr>
          <th>#</th><th>Descripción</th><th>Cant.</th><th>P.Unitario</th><th>Total</th>
        </tr>
      </thead>
      <tbody>
      <?php 
        $j = 1;
        $subtotalServicios = 0;
        foreach($detServ as $d):
          $linea = $d['PrecioUnitario'] * $d['Cantidad'];
          $subtotalServicios += $linea;
      ?>
        <tr>
          <td><?= $j++ ?></td>
          <td><?= htmlspecialchars($d['serv_nombre']) ?></td>
          <td class="right"><?= $d['Cantidad'] ?></td>
          <td class="right"><?= number_format($d['PrecioUnitario'],2) ?></td>
          <td class="right"><?= number_format($linea,2) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <?php
      // Calculamos monto de descuento y neto servicios
      $montoDescServ =  $descuentoServicio;
      $netoServicios = $subtotalServicios - $montoDescServ;
      $igvServicios  = $netoServicios * ($cot['ImpuestoID'] == 1 ? 0 : 0.18);
      $totalConIGVServ = $netoServicios + $igvServicios;
    ?>

 <div class="totals">
  <p><strong>Subtotal Servicios:</strong> S/. <?= number_format($subtotalServicios,2) ?></p>
  <p>
    <strong>Descuento Servicios :</strong>
    S/. <?= number_format($montoDescServ,2) ?>
  </p>
  <p><strong>Neto Servicios:</strong> S/. <?= number_format($netoServicios,2) ?></p>
  <p>
    <strong>IGV Servicios (<?= $cot['ImpuestoID'] == 1 ? '0' : '18' ?>%):</strong>
    S/. <?= number_format($igvServicios,2) ?>
  </p>
  <p><strong>Total Servicios:</strong> S/. <?= number_format($totalConIGVServ,2) ?></p>
</div>
<br>
<div class="totals">
    <p><strong>TOTAL GENERAL:</strong> S/. <?= number_format($totalGeneral,2) ?></p>
  </div>
<?php if ($totalConIGVServ > 699): ?>
  <?php
    // Cálculos de detracción y depósito directo
    $porcentajeDetraccion = 0.12;
    $montoDetraccion     = $totalConIGVServ * $porcentajeDetraccion;
    $montoDirecto        = $totalConIGVServ - $montoDetraccion;
  ?>
  <p><strong>Sujeto a detracciones</strong> </p>
  <table style="width:100%; border-collapse: collapse; border: 1px solid #000; margin-top:1em;">
    <thead>
      <tr>
        <th style="border:1px solid #000; padding:8px; text-align:left;">Concepto</th>
        <th style="border:1px solid #000; padding:8px; text-align:left;">Cuenta</th>
        <th style="border:1px solid #000; padding:8px; text-align:right;">Monto a depositar</th>
      </tr>
    </thead>
    <tbody>
      <!-- Depósito directo -->
      <tr>
        <td style="border:1px solid #000; padding:8px; font-weight:bold;">Directamente</td>
        <td style="border:1px solid #000; padding:8px;">
          <strong>BCP:</strong> 33503851999024<br>
          <strong>Cuenta Interbancaria:</strong> 0023350385199902485
        </td>
        <td style="border:1px solid #000; padding:8px; text-align:right;">
          S/. <?= number_format($montoDirecto,2) ?>
        </td>
      </tr>
      <!-- Depósito de detracción -->
      <tr>
        <td style="border:1px solid #000; padding:8px; background:#ff0; font-weight:bold;">Cuenta de detracciones(12%)</td>
        <td style="border:1px solid #000; padding:8px; background:#ff0;">
          <strong>Cuenta Corriente:</strong> 00-321-134017<br>
          <strong>Código Interbancario:</strong> 01832100032113401703
        </td>
        <td style="border:1px solid #000; padding:8px; background:#ff0; text-align:right;">
          S/. <?= number_format($montoDetraccion,2) ?>
        </td>
      </tr>
    </tbody>
  </table>
<?php endif; ?>


    <?php if ($igvServicios == 0): ?>
      <p style="color:red; font-weight:bold; margin-top:5px;">
        Nota: la cotización de servicios NO incluye IGV
      </p>
    <?php endif; ?>
  <?php endif; ?>

  

  <?php
    // Total general unificando ambas secciones
    $totalGeneral = (isset($totalConIGVProd) ? $totalConIGVProd : 0) + (isset($totalConIGVServ) ? $totalConIGVServ : 0);
  ?>


  <div class="notas">
 
    <?php if (count($detServ)): ?>
      <p>Para iniciar el trabajo, se requiere un pago del 50% del monto total de mano de obra.</p>
    <?php endif; ?>
    <p>
      <?= htmlspecialchars($empresa['nombre_comercial'] ?? '') ?> - RUC: <?= htmlspecialchars($empresa['ruc'] ?? '') ?><br>
      Celular: +51 948 793 154<br>
      <?= htmlspecialchars($empresa['distrito'] ?? '') ?><br>
      <?= htmlspecialchars($fechaHoy) ?>
    </p>
  </div>
 
  <p style="font-size:10px;"><strong>www.sehuacho.com</strong></p>
</body>

</html>
<?php


$html = ob_get_clean();

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);
$options->setChroot(__DIR__);

$dompdf = new Dompdf($options);
try {
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("N°_{$cot_id}_{$cot['Nombre']}_-_{$cot['nombre']} {$cot['apellido_paterno']} {$cot['apellido_materno']}.pdf", ["Attachment" => false]);
} catch (Exception $e) {
    error_log("Error generando PDF: " . $e->getMessage());
    die("Error al generar PDF: " . $e->getMessage());
}
exit;
