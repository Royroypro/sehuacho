<?php
require '../app/config.php'; // Asegúrate de incluir tu archivo de conexión

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $stmt = $pdo->prepare("SELECT id_cliente, nombre, dni_ruc FROM clientes WHERE nombre LIKE :query OR dni_ruc LIKE :query");
    $stmt->execute([':query' => "%$query%"]);
    $options = "";
    while ($fila = $stmt->fetch()) {
        $options .= "<option value='" . $fila['id_cliente'] . "'>" . $fila['nombre'] . " - " . $fila['dni_ruc'] . "</option>";
    }

    
  
                
    echo $options ?: "<option value=''>No se encontraron resultados</option>";
}
?>
