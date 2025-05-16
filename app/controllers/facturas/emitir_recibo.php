<?php

require '../../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $idCliente = $_POST['id_cliente'] ?? '';
    $numeroRecibo = $_POST['numero_recibo'] ?? '';
    $montoUnitario = $_POST['monto'] ?? '';
    $descuento = $_POST['descuento'] ?? '';
    $motivoDescuento = $_POST['motivo_descuento'] ?? '';
    $montoTotal = $_POST['monto_total'] ?? '';
    $igv = $_POST['igv'] ?? '';
    $fechaEmision = $_POST['Fecha_inicio'] ?? '';
    $fechaVencimiento = $_POST['Fecha_finalizacion'] ?? '';
    $subtotal = $_POST['subtotal'] ?? '';
    $mesActual = date('Ym');
    $stmt = $pdo->prepare("SELECT * FROM recibos WHERE id_cliente = :id_cliente AND MONTH(fecha_emision) = MONTH(:fecha_emision) AND YEAR(fecha_emision) = YEAR(:fecha_emision)");
    $stmt->execute(['id_cliente' => $idCliente, 'fecha_emision' => $fechaEmision]);
    $hayRecibo = $stmt->fetch(PDO::FETCH_NUM) ? true : false;
    if ($hayRecibo) {
        die('Recibo ya emitido para este mes para este cliente');
    }
    
    // Por el id_cliente traer id_plan asociado
    $stmt = $pdo->prepare("SELECT cp.id_planes_servicios, c.dni_ruc, c.tipo_documento FROM cliente_planes cp JOIN clientes c ON cp.id_cliente = c.id_cliente WHERE cp.id_cliente = :id_cliente");
    $stmt->execute(['id_cliente' => $idCliente]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    $idPlan = $fila['id_planes_servicios'] ?? '';
    $dniRuc = $fila['dni_ruc'] ?? '';
    $tipoDocumento = $fila['tipo_documento'] ?? '';

    $id_emisor = 1;
    
    // Validar los datos recibidos
    if (empty($idCliente) || empty($fechaEmision) || empty($fechaVencimiento)) {
        die('Por favor complete todos los campos requeridos.');
    }

    // Imprimir los datos recibidos
    echo "ID Cliente: " . htmlspecialchars($idCliente) . "<br>";
    echo "Monto Unitario: " . htmlspecialchars($montoUnitario) . "<br>";
    echo "Descuento: " . htmlspecialchars($descuento) . "<br>";
    echo "Motivo Descuento: " . htmlspecialchars($motivoDescuento) . "<br>";
    echo "Subtotal: " . htmlspecialchars($subtotal) . "<br>";
    echo "Monto Total: " . htmlspecialchars($montoTotal) . "<br>";
    echo "IGV: " . htmlspecialchars($igv) . "<br>";
    echo "Fecha de Emisión: " . htmlspecialchars($fechaEmision) . "<br>";
    echo "Fecha de Vencimiento: " . htmlspecialchars($fechaVencimiento) . "<br>";
    echo "ID Plan: " . htmlspecialchars($idPlan) . "<br>";
    echo "Tipo de Documento: " . htmlspecialchars($tipoDocumento) . "<br>";
    echo "DNI/RUC: " . htmlspecialchars($dniRuc) . "<br>";
    echo "ID Emisor: " . htmlspecialchars($id_emisor) . "<br>";
    echo "Numero Recibo: " . htmlspecialchars($numeroRecibo) . "<br>";
    
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codigoRecibo = '';
    for ($i = 0; $i < 5; $i++) {
        $codigoRecibo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    $codigoRecibo .= substr($numeroRecibo, -2);
    echo "Código de Recibo: " . htmlspecialchars($codigoRecibo) . "<br>";
    
 
    // Aquí puedes realizar operaciones con los datos recibidos, como guardarlos en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO recibos (id_cliente, id_plan_servicio, id_emisor, fecha_emision, fecha_vencimiento, monto_unitario, descuento, motivo_descuento, subtotal, monto_total, igv, numero_recibo, dni_ruc, tipo_documento, codigo_recibo) VALUES (:id_cliente, :id_plan_servicio, :id_emisor, :fecha_emision, :fecha_vencimiento, :monto_unitario, :descuento, :motivo_descuento, :subtotal, :monto_total, :igv, :numero_recibo, :dni_ruc, :tipo_documento, :codigo_recibo)");
        $stmt->execute([
            ':id_cliente' => $idCliente,
            ':id_plan_servicio' => $idPlan,
            ':id_emisor' => $id_emisor,
            ':fecha_emision' => $fechaEmision,
            ':fecha_vencimiento' => $fechaVencimiento,
            ':monto_unitario' => $montoUnitario,
            ':descuento' => $descuento,
            ':motivo_descuento' => $motivoDescuento,
            ':subtotal' => $subtotal,
            ':monto_total' => $montoTotal,
            ':igv' => $igv,
            ':numero_recibo' => $numeroRecibo,
            ':dni_ruc' => $dniRuc,
            ':tipo_documento' => $tipoDocumento,
            ':codigo_recibo' => $codigoRecibo,
        ]);
        header('Location: ' . $URL . '/facturas/lista_recibos');
    } catch (PDOException $e) {
        die("Error al emitir el recibo: " . $e->getMessage());
    }
}

