<?php
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

require __DIR__.'/vendor/autoload.php';

$see = require __DIR__.'/config.php';

// Configuración de cabeceras para respuesta en JSON
header('Content-Type: application/json');

// Verificamos si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos desde el body de la solicitud (se espera JSON)
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (!$inputData) {
        echo json_encode(['error' => 'Datos inválidos o mal formateados']);
        exit();
    }

    // Asignar los datos de la solicitud a variables
    $tipoDocCliente = $inputData['tipoDocCliente'];
    $numDoc = $inputData['numDoc'];
    $rznSocial = $inputData['rznSocial'];
    $ruc = $inputData['ruc'];
    $razonSocial = $inputData['razonSocial'];
    $nombreComercial = $inputData['nombreComercial'];
    $ubigueo = $inputData['ubigueo'];
    $departamento = $inputData['departamento'];
    $provincia = $inputData['provincia'];
    $distrito = $inputData['distrito'];
    $urbanizacion = $inputData['urbanizacion'];
    $direccion = $inputData['direccion'];
    $codLocal = $inputData['codLocal'];
    $tipoDocVenta = $inputData['tipoDocVenta'];
    $tipoOperacion = $inputData['tipoOperacion'];
    $serie = $inputData['serie'];
    $correlativo = $inputData['correlativo'];
    $fechaEmision = $inputData['fechaEmision'];
    $formaPago = $inputData['formaPago'];
    $tipoMoneda = $inputData['tipoMoneda'];
    $codProducto = $inputData['codProducto'];
    $unidad = $inputData['unidad'];
    $cantidad = $inputData['cantidad'];
    $valorUnitario = $inputData['valorUnitario'];
    $descripcion = $inputData['descripcion'];
    $igv = 18;
    $tipAfeIgv = '10';

    try {
        // Cliente
        $client = (new Client())
            ->setTipoDoc($tipoDocCliente)
            ->setNumDoc($numDoc)
            ->setRznSocial($rznSocial);

        // Emisor
        $address = (new Address())
            ->setUbigueo($ubigueo)
            ->setDepartamento($departamento)
            ->setProvincia($provincia)
            ->setDistrito($distrito)
            ->setUrbanizacion($urbanizacion)
            ->setDireccion($direccion)
            ->setCodLocal($codLocal); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

        $company = (new Company())
            ->setRuc($ruc)
            ->setRazonSocial($razonSocial)
            ->setNombreComercial($nombreComercial)
            ->setAddress($address);

        // Venta
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion($tipoOperacion)
            ->setTipoDoc($tipoDocVenta)
            ->setSerie($serie)
            ->setCorrelativo($correlativo)
            ->setFechaEmision(new DateTime($fechaEmision))
            ->setFormaPago(new FormaPagoContado())
            ->setTipoMoneda($tipoMoneda)
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas($valorUnitario * $cantidad)
            ->setMtoIGV($valorUnitario * $cantidad * $igv / 100)
            ->setTotalImpuestos($valorUnitario * $cantidad * $igv / 100)
            ->setValorVenta($valorUnitario * $cantidad)
            ->setSubTotal($valorUnitario * $cantidad + $valorUnitario * $cantidad * $igv / 100)
            ->setMtoImpVenta($valorUnitario * $cantidad + $valorUnitario * $cantidad * $igv / 100)
        ;

        $item = (new SaleDetail())
            ->setCodProducto($codProducto)
            ->setUnidad($unidad)
            ->setCantidad($cantidad)
            ->setMtoValorUnitario($valorUnitario)
            ->setDescripcion($descripcion)
            ->setMtoBaseIgv($valorUnitario * $cantidad)
            ->setPorcentajeIgv($igv)
            ->setIgv($valorUnitario * $cantidad * $igv / 100)
            ->setTipAfeIgv($tipAfeIgv)
            ->setTotalImpuestos($valorUnitario * $cantidad * $igv / 100)
            ->setMtoValorVenta($valorUnitario * $cantidad)
            ->setMtoPrecioUnitario($valorUnitario * (1 + $igv / 100));

        $legend = (new Legend())
            ->setCode('1000')
            ->setValue('SON '.number_format($valorUnitario * $cantidad, 2).' CON 00/100 SOLES');

        $invoice->setDetails([$item])
            ->setLegends([$legend]);

        $result = $see->send($invoice);

        // Guardar XML firmado digitalmente
        $xmlContent = $see->getFactory()->getLastXml();
        file_put_contents('facturas/'.$invoice->getName().'.xml', $xmlContent);

        // Verificamos que la conexión con SUNAT fue exitosa
        if (!$result->isSuccess()) {
            echo json_encode([
                'error' => 'Error al conectarse con SUNAT',
                'codigo_error' => $result->getError()->getCode(),
                'mensaje_error' => $result->getError()->getMessage()
            ]);
            exit();
        }

        // Guardamos el CDR
        $cdrZip = $result->getCdrZip();
        file_put_contents('respuestas/R-'.$invoice->getName().'.zip', $cdrZip);

        $cdrBase64 = base64_encode($cdrZip);
        $xmlBase64 = base64_encode($xmlContent);
        $cdr = $result->getCdrResponse();
        $code = (int)$cdr->getCode();

        $response = [
            'estado' => '',
            'mensaje' => $cdr->getDescription(),
            'codigo' => $code,
            'cdr_base64' => $cdrBase64,
            'xml_base64' => $xmlBase64,
        ];

        if ($code === 0) {
            $response['estado'] = 'ACEPTADA';
            if (count($cdr->getNotes()) > 0) {
                $response['observaciones'] = $cdr->getNotes();
            }
        } else if ($code >= 2000 && $code <= 3999) {
            $response['estado'] = 'RECHAZADA';
        } else {
            $response['estado'] = 'EXCEPCIÓN';
        }

        echo json_encode($response);

    } catch (Exception $e) {
        echo json_encode(['error' => 'Hubo un error procesando la solicitud', 'message' => $e->getMessage()]);
    }
}
?>
