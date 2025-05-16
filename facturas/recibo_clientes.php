<?php
//comprobacion para regidir al recibo
require '../app/config.php';

if (isset($_GET['id_recibo'])) {
    $stmt = $pdo->prepare("SELECT estado FROM recibos WHERE id_recibo = :id_recibo");
    $stmt->execute([':id_recibo' => $_GET['id_recibo']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['estado'] === 'PAGADO') {
        header('Location: descargar_recibo_pagado_cliente.php?id_recibo=' . $_GET['id_recibo']);
        exit;
    } else {
        header('Location: recibo_cliente_no_pagado.php?id_recibo=' . $_GET['id_recibo']);
        exit;
    }
}
