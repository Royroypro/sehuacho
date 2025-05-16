<?php
// Ajusta la ruta si tu config está en otra carpeta
include_once '../app/config.php';

$cot_id = (int)($_GET['id'] ?? 0);
if (!$cot_id) {
  die('ID de cotización inválido');
}

// Obtener cotización y datos del cliente
$stmt = $pdo->prepare("
    SELECT 
      c.CotizacionID,
      c.Nombre,
      c.FechaCotizacion,
      c.FechaValidez,
      c.Estado,
      c.Subtotal,
      c.Impuestos,
      c.Total,
      cli.id_cliente,
      cli.nombre,
      cli.apellido_paterno,
      cli.apellido_materno,
      cli.celular
    FROM cotizaciones AS c
    INNER JOIN clientes AS cli 
      ON cli.id_cliente = c.ClienteID
    WHERE c.CotizacionID = :id
");
$stmt->execute([':id' => $cot_id]);
$cot = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cot) {
  die('Cotización no encontrada');
}

// Construir URLs
$pdfUrl = "generar_pdf.php?id={$cot_id}";

// Preparar teléfono con código de país (Perú +51)
$soloDigitos = $cot['celular'] === null ? '' : preg_replace('/\D+/', '', $cot['celular']);
$telefonoWs = '51' . ltrim($soloDigitos, '0');

$linkVer = "https://royner.ddns.net/ver_cotizacion.php?id={$cot_id}";
$wspMsg = urlencode("Tu cotización (#{$cot_id}) está lista: {$linkVer}");
$wspUrl = "https://api.whatsapp.com/send?phone={$telefonoWs}&text={$wspMsg}";
?>


<div id="main-wrapper">
  <?php include '../layout/parte1.php'; ?>

  <div class="page-wrapper">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="container">
            <div class="card-body">
              <h1 class="mb-4">Cotización #<?= htmlspecialchars($cot['CotizacionID']) ?></h1>
              <dl class="row">
                <dt class="col-sm-3">Cliente</dt>
                <dd class="col-sm-9">
                  <?= htmlspecialchars("{$cot['nombre']} {$cot['apellido_paterno']} {$cot['apellido_materno']}") ?>
                </dd>
                <dt class="col-sm-3">Fecha de Cotización</dt>
                <dd class="col-sm-9"><?= htmlspecialchars($cot['FechaCotizacion']) ?></dd>

                <dt class="col-sm-3">Validez Hasta</dt>
                <dd class="col-sm-9"><?= htmlspecialchars($cot['FechaValidez'] ?? '—') ?></dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9"><?= htmlspecialchars($cot['Estado']) ?></dd>

                <dt class="col-sm-3">Subtotal</dt>
                <dd class="col-sm-9"><?= number_format($cot['Subtotal'], 2) ?></dd>

                <dt class="col-sm-3">Impuestos</dt>
                <dd class="col-sm-9"><?= number_format($cot['Impuestos'], 2) ?></dd>

                <dt class="col-sm-3">Total</dt>
                <dd class="col-sm-9 font-weight-bold"><?= number_format($cot['Total'], 2) ?></dd>
              </dl>

              <div class="mt-4">
                <a href="<?= $pdfUrl ?>" class="btn btn-primary mr-2">Descargar PDF</a>
                <a href="<?= $wspUrl ?>" target="_blank" class="btn btn-success">Enviar por WhatsApp</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php include '../layout/parte2.php'; ?>