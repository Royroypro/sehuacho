<?php
include_once '../app/config.php'; // Configuración de la base de datos y otras configuraciones
include_once '../app/controllers/clientes/consultar_cliente.php'; // Cambiar a la consulta de clientes
?>

<div id="main-wrapper">
    <?php

    include_once '../layout/parte1.php';

    $id = isset($_GET['id']) ? $_GET['id'] : null;
    ?>

    <div class="page-wrapper">
        <?php
        if ($id) {
            $stmt->execute();
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($cliente) {
                $nombre = htmlspecialchars($cliente['nombre'], ENT_QUOTES, 'UTF-8');
            }
        }
        ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Eliminar Cliente</h4>
                        <h6 class="card-subtitle">¿Está seguro de eliminar el cliente?</h6>
                        <!-- <form class="form-horizontal m-t-30" action="../app/controllers/clientes/eliminar_cliente.php" method="post"> -->
                        <form id="eliminarClienteForm" class="form-horizontal m-t-30" method="post">
                            <input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo $id; ?>">

                            <div class="form-group">
                                <label for="nombre" class="col-sm-12">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="nombre" name="nombre_cliente" placeholder="Ingrese el nombre del cliente" value="<?php echo $nombre; ?>" required readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </div>
                            </div>
                        </form>

                        <!-- Contenedor para mensajes -->
                        <div id="mensajeExito" style="display:none; color:green; font-weight:bold;" aria-live="polite"></div>

                        <!-- JavaScript -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            $(document).ready(function() {
                                $("#eliminarClienteForm").on("submit", function(e) {
                                    e.preventDefault(); // Evitar que se recargue la página

                                    // Obtener datos del formulario
                                    const datos = {
                                        id_cliente: $("input[name='id_cliente']").val()
                                    };

                                    // Enviar datos usando AJAX
                                    $.ajax({
                                        url: "../app/controllers/clientes/eliminar_cliente.php", // Archivo PHP que procesa la solicitud
                                        method: "POST",
                                        data: datos, // Enviar datos
                                        dataType: "json", // Asegurarse de recibir JSON
                                        success: function(respuesta) {
                                            if (respuesta && respuesta.success) {
                                                $("#mensajeExito").text(respuesta.message).show();
                                                setTimeout(function() {
                                                    window.location.href = "lista_cliente.php"; // Redirigir a la lista de clientes
                                                }, 1000);
                                            } else {
                                                alert(respuesta ? respuesta.message : "Error inesperado.");
                                            }
                                        },
                                        error: function() {
                                            alert("Ocurrió un error al eliminar el cliente.");
                                        }
                                    });
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>
</div>
</div>
</div>
</div>

<?php include('../layout/parte2.php'); ?>