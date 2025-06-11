<?php

// Ajusta la ruta si tu config está en otra carpeta
include_once '../app/config.php';

$codigo = (string)($_GET['codigo'] ?? '');
if (empty($codigo)) {
    die('Código de cotización inválido');
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
    WHERE c.Codigo = :codigo
");
$stmt->execute([':codigo' => $codigo]);
$cot = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cot) {
    die('Cotización no encontrada');
}

// Construir URLs
$pdfUrl = "generar_pdf.php?id={$cot['CotizacionID']}";

// Preparar teléfono con código de país (Perú +51)
$soloDigitos = $cot['celular'] === null ? '' : preg_replace('/\D+/', '', $cot['celular']);
$telefonoWs = '51' . ltrim($soloDigitos, '0');
$nombre_cotizacion = $cot['Nombre'];
$linkVer = "{$URL}/cotizaciones/ver_para_cliente.php?codigo={$codigo}";
$wspMsg = urlencode("Tu cotización de {$nombre_cotizacion}  (#{$codigo}) está lista: {$linkVer}");
$wspUrl = "https://api.whatsapp.com/send?text={$wspMsg}";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEGURIDAD ELECTRONICA HUACHO</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 2rem;
            color: #333;
        }
        dl.row dt {
            font-weight: 600;
            color: #555;
        }
        dl.row dd {
            margin-bottom: 1rem;
        }
        .btn-primary {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
        }
        .btn-success {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
        }
    </style>
</head>
<body>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card p-4">
                        <div class="mb-4 text-center">
                            <h1>Cotización #<?= htmlspecialchars($cot['CotizacionID']) ?></h1>
                            <small class="text-muted">Emitida el <?= htmlspecialchars(date('d/m/Y', strtotime($cot['FechaCotizacion']))) ?><?php if ($cot['FechaValidez']): ?> · Válida hasta <?= htmlspecialchars(date('d/m/Y', strtotime($cot['FechaValidez']))) ?><?php endif; ?></small>
                        </div>

                        <dl class="row">
                            <dt class="col-sm-4">Cliente:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars("{$cot['nombre']} {$cot['apellido_paterno']} {$cot['apellido_materno']}") ?></dd>

                            <dt class="col-sm-4">Estado:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($cot['Estado']) ?></dd>

                            <dt class="col-sm-4">Subtotal:</dt>
                            <dd class="col-sm-8">S/ <?= number_format($cot['Subtotal'], 2) ?></dd>

                            <dt class="col-sm-4">Impuestos:</dt>
                            <dd class="col-sm-8">S/ <?= number_format($cot['Impuestos'], 2) ?></dd>

                            <dt class="col-sm-4">Total:</dt>
                            <dd class="col-sm-8 fw-bold">S/ <?= number_format($cot['Total'], 2) ?></dd>
                        </dl>
                        <div class="text-center mt-4">
                            <p>Para más información contactar a  <strong>+51 948 793 154</strong></a>.</p>
                        </div>


                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="<?= $pdfUrl ?>" class="btn btn-primary">Descargar PDF</a>
                            <a href="<?= $wspUrl ?>" target="_blank" class="btn btn-success">Enviar por WhatsApp</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap 5 JS y dependencias (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>