


<?php
include_once '../app/config.php';
$codigo_recibo = $_GET['codigo_recibo'] ?? '';

if (empty($codigo_recibo)) {
    die('No se ha proporcionado el código de recibo');
}

$stmt = $pdo->prepare("SELECT r.id_recibo, r.numero_recibo, r.Tipo_documento, r.dni_ruc, r.id_cliente, r.id_emisor, r.id_plan_servicio, r.fecha_emision, r.fecha_vencimiento, r.monto_unitario, r.descuento, r.monto_total, r.igv, r.estado, r.fecha_pago, r.motivo_descuento, r.fecha_creacion, r.fecha_actualizacion, r.subtotal
FROM recibos r
WHERE r.codigo_recibo = :codigo_recibo");

$stmt->execute(['codigo_recibo' => $codigo_recibo]);

$recibo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($recibo)) {
    echo "<!DOCTYPE html>";
    echo "<html lang='es'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Recibo</title>";
    echo "<link rel='stylesheet' href='styles.css'>"; // Enlace a la hoja de estilos
    echo "</head>";
    echo "<body>";
    echo "<div class='container'>";
    echo "<header>";
    echo "<img src='logo.png' alt='Logo'>"; // Añade tu logo aquí
    echo "<h2>Este recibo es auténtico</h2>";
    echo "</header>";
    echo "<p>Número de recibo: <strong>" . $recibo['numero_recibo'] . "</strong></p>";
    echo "<p>Fecha de emisión: <strong>" . $recibo['fecha_emision'] . "</strong></p>";
    echo "<p>Monto Total: <strong>" . $recibo['monto_total'] . "</strong></p>";
    echo "<p>Estado: <strong>" . ($recibo['estado'] !== 'PAGADO' ? 'NO PAGADO' : $recibo['estado']) . "</strong></p>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
} else {
    die('El código de recibo no existe');
}
?>


