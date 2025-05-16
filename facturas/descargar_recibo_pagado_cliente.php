<?php

require '../app/config.php';

$id = isset($_GET['id_recibo']) ? $_GET['id_recibo'] : null;


$stmt = $pdo->prepare("SELECT r.id_recibo, r.codigo_recibo, r.numero_recibo, r.Tipo_documento, r.dni_ruc, r.id_cliente, r.id_emisor, r.id_plan_servicio, r.fecha_emision, r.fecha_vencimiento, r.monto_unitario, r.descuento, r.monto_total, r.igv, r.estado, r.fecha_pago, r.motivo_descuento, r.fecha_creacion, r.fecha_actualizacion, r.subtotal
FROM recibos r
WHERE r.id_recibo = :id_recibo");

$stmt->execute(['id_recibo' => $id]);

$recibo = $stmt->fetch(PDO::FETCH_ASSOC);


//cliente
$tipo_documento = $recibo['Tipo_documento'] == 'DNI' ? 1 : 6;
$dni_ruc = $recibo['dni_ruc'];
$stmt = $pdo->prepare("SELECT nombre, apellido_paterno, apellido_materno, celular FROM clientes WHERE id_cliente = :id_cliente");
$stmt->execute(['id_cliente' => $recibo['id_cliente']]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre_completo = "{$cliente['nombre']} {$cliente['apellido_paterno']} {$cliente['apellido_materno']}";
$celular = $cliente['celular'];


//emisor

//datos del emisor
$id_emisor = $recibo['id_emisor'];
$stmt = $pdo->prepare("SELECT ruc, razon_social, nombre_comercial, id_direccion FROM empresas WHERE id = :id_empresa");
$stmt->execute(['id_empresa' => $id_emisor]);
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);
$ruc = $empresa['ruc'];
$razon_social = $empresa['razon_social'];
$nombre_comercial = $empresa['nombre_comercial'] ?? ''; // nombre_comercial puede ser nulo
$id_direccion = $empresa['id_direccion'];


//direccion del emisor
$stmt = $pdo->prepare("SELECT d.ubigeo, d.departamento, d.provincia, d.distrito, d.urbanizacion, d.direccion, d.cod_local 
FROM direccion d 
WHERE d.id = :id_direccion");
$stmt->execute(['id_direccion' => $id_direccion]);
$direccion = $stmt->fetch(PDO::FETCH_ASSOC);
$ubigeo = $direccion['ubigeo'];
$departamento = $direccion['departamento'];
$provincia = $direccion['provincia'];
$distrito = $direccion['distrito'];
$urbanizacion = $direccion['urbanizacion'];
$direccion_domicilio = $direccion['direccion'];
$cod_local = $direccion['cod_local'];




$tipo_recibo = $recibo['Tipo_documento'] == 'DNI' ? '03' : '01';
$serie_recibo = substr($recibo['numero_recibo'], 0, 4);
$correlativo_recibo = substr($recibo['numero_recibo'], 5);
$fecha_emision = (new DateTime($recibo['fecha_emision']))->format('Y-m-d H:i:sP');



$stmt = $pdo->prepare("SELECT nombre_plan FROM planes_servicio WHERE id_plan_servicio = :id_plan_servicio");
$stmt->execute(['id_plan_servicio' => $recibo['id_plan_servicio']]);
$nombre_plan = $stmt->fetchColumn();
$fecha_vencimiento = $recibo['fecha_vencimiento'];
$montoUnitario = $recibo['monto_unitario'];
$descuento = $recibo['descuento'];
$subtotal = $recibo['subtotal'];
$mtoIGV = $recibo['igv'];
$montoTotal = $recibo['monto_total'];




$estado = $recibo['estado'];
$fecha_pago = $recibo['fecha_pago'];
$motivo_descuento = $recibo['motivo_descuento'];
$fecha_creacion = $recibo['fecha_creacion'];
$fecha_actualizacion = $recibo['fecha_actualizacion'];

function convertirNumeroALetras($numero)
{
    $formatter = new NumberFormatter("es", NumberFormatter::SPELLOUT);
    return ucfirst($formatter->format($numero));
}


// Separar parte entera y decimal
$parteEntera = floor($montoTotal);
$parteDecimal = round(($montoTotal - $parteEntera) * 100);

// Convertir parte entera a letras
$parteEnteraLetras = convertirNumeroALetras($parteEntera);

// Concatenar con la parte decimal
$montoEnLetras = strtoupper("Son " . $parteEnteraLetras . " con " . str_pad($parteDecimal, 2, "0", STR_PAD_LEFT) . "/100 SOLES");





use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

require __DIR__ . '/vendor/autoload.php';

$see = require __DIR__ . '/config.php';

$client = (new Client())
    // 1 = DNI, 6 = RUC
    ->setTipoDoc($tipo_documento)
    ->setNumDoc($dni_ruc)
    ->setRznSocial($nombre_completo);

// Emisor
$address = (new Address())
    ->setUbigueo('150201')
    ->setDepartamento($departamento)
    ->setProvincia($provincia)
    ->setDistrito($distrito)
    ->setUrbanizacion($urbanizacion)
    ->setDireccion($direccion_domicilio)
    ->setCodLocal('0000'); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

$company = (new Company())
    ->setRuc($ruc)
    ->setRazonSocial($razon_social)
    ->setNombreComercial($nombre_comercial)
    ->setAddress($address);

// Venta
$invoice = (new Invoice())
    ->setUblVersion('2.1')
    ->setTipoOperacion('0101') // Venta - Catalog. 51
    ->setTipoDoc($tipo_recibo) // Factura - Catalog. 01 
    ->setSerie($serie_recibo)
    ->setCorrelativo($correlativo_recibo)
    ->setFechaEmision(new DateTime($fecha_emision))
    ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
    ->setTipoMoneda('PEN') // Sol - Catalog. 02
    ->setCompany($company)
    ->setClient($client)
    ->setMtoOperGravadas($subtotal)
    ->setMtoIGV($mtoIGV)
    ->setTotalImpuestos($mtoIGV)
    ->setValorVenta($subtotal)
    ->setSubTotal($montoTotal)
    ->setMtoImpVenta($montoTotal);

$item = (new SaleDetail())
    //para productos
    /* ->setCodProducto('P001') */
    //para servicios
    ->setCodProducto('S001')
    //para productos 

    /*  ->setUnidad('NIU') // Unidad - Catalog. 03 */

    ->setUnidad('ZZ') // Unidad - Catalog. 03 (ZZ = servicio)
    ->setCantidad(1)
    ->setMtoValorUnitario($subtotal)
    ->setDescripcion('PRODUCTO 1')
    ->setMtoBaseIgv($subtotal)
    ->setPorcentajeIgv(18.00) // 18%
    ->setIgv($mtoIGV)
    ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
    ->setTotalImpuestos($mtoIGV) // Suma de impuestos en el detalle
    ->setMtoValorVenta($subtotal)
    ->setMtoPrecioUnitario($montoTotal);

$legend = (new Legend())
    ->setCode('1000') // Monto en letras - Catalog. 52
    ->setValue($montoEnLetras);

$invoice->setDetails([$item])
    ->setLegends([$legend]);
$result = $see->send($invoice);

// Guardar XML firmado digitalmente.
$xmlName = $invoice->getName() . '.xml';
file_put_contents($xmlName, $see->getFactory()->getLastXml());
rename($xmlName, 'xml/' . $xmlName);

$dom = new \DOMDocument();
$dom->loadXML(file_get_contents('xml/' . $xmlName));
$digestValue = $dom->getElementsByTagName('DigestValue')->item(0)->nodeValue;



//datos del qr

$recibo['codigo_recibo'];
$razon_social;
$urlRecibo = "$URL/facturas/comprobacion_recibo.php?codigo_recibo=" . $recibo['codigo_recibo'];



use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Sabberworm\CSS\Value\URL;

$builder = new Builder(
    writer: new PngWriter(),
    writerOptions: [],
    validateResult: false,
    data: "$recibo[codigo_recibo] | $razon_social | $urlRecibo",
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: ErrorCorrectionLevel::High,
    size: 300,
    margin: 10,
    roundBlockSizeMode: RoundBlockSizeMode::Margin,
    logoPath: __DIR__ . '/logo.png',
    logoResizeToWidth: 150,
    logoPunchoutBackground: true,

    labelFont: new OpenSans(20),
    labelAlignment: LabelAlignment::Center
);


$result = $builder->build();


$qrName = pathinfo($xmlName, PATHINFO_FILENAME);
$qrDirectory = __DIR__ . '/qr/';
$qrFileName = $qrName . '.png';
$result->saveToFile($qrDirectory . $qrFileName);



// Aquí va el resto de su código
/* 
ob_end_clean(); // Limpia el búfer de salida y detiene cualquier salida al navegador
 // Ruta del logo
$logoPath = realpath('logo.png'); // Asegúrate de que el archivo 'logo.png' exista en el mismo directorio o proporciona la ruta completa

// Verifica si el archivo del logo existe
if ($logoPath && file_exists($logoPath)) {
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
} else {
    $logoSrc = ''; // En caso de que el logo no esté disponible
}
*/
$qrFileContent = file_get_contents($qrDirectory . $qrFileName);
$qrBase64 = 'data:image/png;base64,' . base64_encode($qrFileContent);

$tipo_documento = $recibo['Tipo_documento'] ?? '';

$codPlan = $pdo->prepare("SELECT codigo_plan FROM planes_servicio WHERE id_plan_servicio = :id_plan_servicio");
$codPlan->execute(['id_plan_servicio' => $recibo['id_plan_servicio']]);
$codPlan = $codPlan->fetchColumn();

$fecha_emision = (new DateTime($recibo['fecha_emision']))->format('d/m/Y');
$fecha_vencimiento = (new DateTime($recibo['fecha_vencimiento']))->format('d/m/Y');
// Ruta del logo
$logoPath = realpath('logo.png'); // Asegúrate de que el archivo 'logo.png' exista en el mismo directorio o proporciona la ruta completa

// Verifica si el archivo del logo existe
if ($logoPath && file_exists($logoPath)) {
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
} else {
    $logoSrc = ''; // En caso de que el logo no esté disponible
}

$qrFileContent = file_get_contents($qrDirectory . $qrFileName);
$qrBase64 = 'data:image/png;base64,' . base64_encode($qrFileContent);

$tipo_documento = $recibo['Tipo_documento'] ?? '';

$codPlan = $pdo->prepare("SELECT codigo_plan FROM planes_servicio WHERE id_plan_servicio = :id_plan_servicio");
$codPlan->execute(['id_plan_servicio' => $recibo['id_plan_servicio']]);
$codPlan = $codPlan->fetchColumn();

$fecha_emision = (new DateTime($recibo['fecha_emision']))->format('d/m/Y');
$fecha_vencimiento = (new DateTime($recibo['fecha_vencimiento']))->format('d/m/Y');

// Ruta del logo
$logoPath = realpath('logo.png'); // Asegúrate de que el archivo 'logo.png' exista en el mismo directorio o proporciona la ruta completa

// Verifica si el archivo del logo existe
if ($logoPath && file_exists($logoPath)) {
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
} else {
    $logoSrc = ''; // En caso de que el logo no esté disponible
}

$qrFileContent = file_get_contents($qrDirectory . $qrFileName);
$qrBase64 = 'data:image/png;base64,' . base64_encode($qrFileContent);

$tipo_documento = $recibo['Tipo_documento'] ?? '';
$tipo_documento_nombre = $tipo_documento == 'DNI' ? 'Boleta' : 'Factura';

$codPlan = $pdo->prepare("SELECT codigo_plan FROM planes_servicio WHERE id_plan_servicio = :id_plan_servicio");
$codPlan->execute(['id_plan_servicio' => $recibo['id_plan_servicio']]);
$codPlan = $codPlan->fetchColumn();

$fecha_emision = (new DateTime($recibo['fecha_emision']))->format('d/m/Y');
$fecha_vencimiento = (new DateTime($recibo['fecha_vencimiento']))->format('d/m/Y');
$html = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.4; text-align: center; width: 300px; height: 700px; margin: 20px auto; border: 2px solid #aaa; padding: 10px; position: relative; }
        .header { margin-bottom: 10px; }
        .logo { margin: 0 auto; display: block; max-width: 80px; max-height: 60px; }
        .columns { display: flex; justify-content: space-between; margin-top: 10px; }
        .details { text-align: left; font-size: 10px; }
        .amount { text-align: right; font-size: 16px; color: #2a2a2a; }
        .section { margin-bottom: 10px; font-size: 10px; }
        .section-title { font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #aaa; padding-bottom: 2px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 10px; }
        table th, table td { border: 2px solid #aaa; padding: 4px; text-align: left; }
        table th { background-color: #f5f5f5; }
        p { margin: 2px 0; }
        .qr-code { margin-top: 5px; width: 180px; height: 180px; }
        .buttons { position: absolute; top: 10px; right: -100px; display: flex; flex-direction: column; }
        .buttons button {
            margin-bottom: 5px;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
        }
        .buttons button:hover {
            background-color: #45a049;
        }
         @media print {
            @page {
                size: A4;
                margin: 0;
            }
            header, footer, nav, .buttons, .no-imprimir, .print-footer, .print-header {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    
    <div class='buttons'>
        <button onclick='printRecibo()'>Imprimir</button>
        
        <button onclick='downloadPDF()'>Descargar</button>
    </div>
    <div class='header'>
        " . (!empty($logoSrc) ? "<img src='{$logoSrc}' alt='Logo' class='logo'>" : "") . "
        <h1 style='font-size: 14px;'>{$tipo_documento_nombre} N° {$recibo['numero_recibo']}</h1>
    </div>
    <h2 style='font-size: 12px;'>Código de {$tipo_documento_nombre}: {$recibo['codigo_recibo']}</h2>
    <div class='columns'>
        <div class='details'>
            <p><strong>Fecha de Emisión:</strong> {$fecha_emision}</p>
            
            <p><strong>ESTADO: <span style='color: #5cb85c;'>{$estado}</span></strong></p>
        </div>
        <div class='amount'>
            <p style='font-size: 10px;'>Monto Total:</p>
            <h1 style='font-size: 14px; color: #5cb85c;'>S/ {$montoTotal}</h1>
        </div>
    </div>
    <div class='section'>
        <div class='section-title'>Datos del Cliente</div>
        <p>Nombre: {$nombre_completo}</p>
        <p>{$tipo_documento}: {$dni_ruc}</p>
    </div>
    <div class='section'>
        <div class='section-title'>Datos del Emisor</div>
        <p>Razón Social: {$razon_social}</p>
        <p>RUC: {$ruc}</p>
        <p>Dirección: {$direccion_domicilio}, {$distrito}, {$provincia}, {$departamento}</p>
    </div>
    <div class='section'>
        <div class='section-title'>Firma Digital</div>
        <p>Hash: {$digestValue}</p>
    </div>
    <div class='section'>
        <div class='section-title'>Detalle del Recibo</div>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Servicio {$nombre_plan} - Código: {$codPlan}</td>
                    <td>1</td>
                    <td>S/ {$subtotal}</td>
                    <td>S/ {$montoTotal}</td>
                </tr>
            </tbody>
        </table>
        <p>Subtotal: S/ {$subtotal}</p>
        <p>IGV: S/ {$mtoIGV}</p>
        <p>Total: S/ {$montoTotal}</p>
        <p>{$montoEnLetras}</p>
    </div>
    <div class='section'>
        <div>
            " . (!empty($qrBase64) ? "<img src='{$qrBase64}' alt='QR Code' class='qr-code'>" : "") . "
        </div>
    </div>
</body>
</html>";

echo $html;
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    function sendEmail() {
        var numeroTelefono = '<?php echo (strpos($celular, "+51") === 0) ? $celular : "+51" . $celular; ?>';
        var link = '<?php echo $URL; ?>/facturas/recibo_cliente.php?id_recibo=<?php echo $recibo["id_recibo"]; ?>';
        var whatsappUrl = 'https://api.whatsapp.com/send?phone=' + numeroTelefono + '&text=Descarga+tu+recibo+aqu%C3%AD%3A+' + encodeURIComponent(link);
        window.open(whatsappUrl, '_blank');
    }

    function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            format: [257, 570],
            unit: 'px',
            orientation: 'portrait',
            compress: true
        });

        const style = document.createElement('style');
        document.head.appendChild(style);

        const content = document.body;
        const buttons = document.querySelectorAll('.buttons');
        buttons.forEach(button => button.style.display = 'none');

        doc.html(content, {
            callback: function(doc) {
                doc.save('<?php echo substr($xmlName, 0, -4); ?>.pdf');
                location.reload();
            },
            x: 0,
            y: 0,
            margin: [-16, 0, -500, 0], // margen superior, derecho, inferior e izquierdo

            // Ancho del contenido del PDF
            width: 257,
            // Alto del contenido del PDF
            height: 570,
            // Ancho de la ventana del navegador
            windowWidth: 257,
            autoPaging: 'disabled',
            html2canvas: {
                scale: 0.8,
                useCORS: true
            }
        });
    }

    function printRecibo() {
        window.print();
    }
</script>
