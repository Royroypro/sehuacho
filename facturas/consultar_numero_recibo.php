<?php

require '../app/config.php';

$tipo_documento = $_POST['tipo_documento'] ?? '';
$stmt = $pdo->prepare("SELECT COALESCE(MAX(SUBSTRING(numero_recibo, 8)), 1) AS numero_recibo FROM recibos WHERE Tipo_documento = :tipo_documento");
$stmt->execute(['tipo_documento' => $tipo_documento]);
$fila = $stmt->fetch(PDO::FETCH_ASSOC);

$numero = $fila['numero_recibo'] ?? 1;

do {
    $numero++;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS existe FROM recibos WHERE Tipo_documento = :tipo_documento AND numero_recibo = :numero_recibo");
    $stmt->execute(['tipo_documento' => $tipo_documento, 'numero_recibo' => $tipo_documento === 'DNI' ? 'B001-' . str_pad($numero, 8, '0', STR_PAD_LEFT) : 'F001-' . str_pad($numero, 8, '0', STR_PAD_LEFT)]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
} while ($fila['existe'] > 0);

if ($tipo_documento === 'DNI') {
    echo 'B001-' . str_pad($numero, 8, '0', STR_PAD_LEFT);
} else {
    echo 'F001-' . str_pad($numero, 8, '0', STR_PAD_LEFT);
}



