<?php
// 0. Autoload y config
require_once __DIR__ . '/../facturas/vendor/autoload.php';
include_once __DIR__ . '/../app/config.php';  // define $pdo (PDO)

date_default_timezone_set('America/Lima');




//datos para el QR
// 3. Cabecera cotización
$sqlCot = "SELECT c.codigo
           FROM cotizaciones c
           WHERE c.CotizacionID = :id";
$stmt = $pdo->prepare($sqlCot);
$stmt->execute([':id' => $cot_id]);
$codigo = $stmt->fetch(PDO::FETCH_ASSOC) ?: die('Cotización no encontrada');

//echo $codigo['codigo']; // Para depurar, puedes comentar esta línea si no es necesario

//datos del qr
$codigo = $codigo['codigo'];
$urlCodigo = "$URL/cotizaciones/ver_para_cliente.php?codigo=" . $codigo;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
$builder = new Builder(
    writer: new PngWriter(),
    writerOptions: [],
    validateResult: false,
    data: "$urlCodigo",
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: ErrorCorrectionLevel::High,
    size: 400,
    margin: 10,
    roundBlockSizeMode: RoundBlockSizeMode::Margin,
    logoPath: __DIR__.'/logo_1.png',
    logoResizeToWidth: 150,
    logoPunchoutBackground: true,
    
    labelFont: new OpenSans(20),
    labelAlignment: LabelAlignment::Center
);

$result = $builder->build();

// Limpiar la carpeta QR antes de guardar
$qrDirectory = __DIR__ . '/qr/';
array_map('unlink', glob("$qrDirectory*.png"));

// Asegurarse de que no haya salida previa
if (ob_get_length()) {
    ob_end_clean();
}

// Directly output the QR code
header('Content-Type: '.$result->getMimeType());

$qrName = $cot_id;
header('Content-Disposition: attachment; filename="'.$qrName.'.png"');
$qrFileName = $qrName . '.png';
$result->saveToFile($qrDirectory . $qrFileName);
// Convertir a base64
$qrBase64 = base64_encode(file_get_contents($qrDirectory . $qrFileName));