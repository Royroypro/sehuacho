<?php
include_once '../app/config.php';
$id = isset($_GET['id']) ? $_GET['id'] : null;

include_once '../app/controllers/productos/consultar_producto.php';
?>
<div id="main-wrapper">
    <?php
    include_once '../layout/parte1.php';
    ?>
    <div class="page-wrapper">
        <?php
        if ($id) {
            $stmt->execute();
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($producto) {
                $nombre = htmlspecialchars($producto['Nombre'], ENT_QUOTES, 'UTF-8');
            }
        }
        ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Eliminar Producto</h4>
                        <h6 class="card-subtitle">¿Está seguro de eliminar el producto?</h6>
                        <form id="eliminarProductoForm" class="form-horizontal m-t-30" method="post">
                            <input type="hidden" name="id_producto" value="<?php echo $id; ?>">

                            <div class="form-group">
                                <label for="nombre" class="col-sm-12">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="nombre" name="nombre_producto" placeholder="Ingrese el nombre del producto" value="<?php echo $nombre; ?>" required readonly>
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
                                $("#eliminarProductoForm").on("submit", function(e) {
                                    e.preventDefault(); // Evitar que se recargue la página

                                    // Obtener datos del formulario
                                    const datos = {
                                        id_producto: $("input[name='id_producto']").val()
                                    };

                                    // Enviar datos usando AJAX
                                    $.ajax({
                                        url: "../app/controllers/productos/eliminar_producto.php", // Archivo PHP que procesa la solicitud
                                        method: "POST",
                                        data: datos, // Enviar datos
                                        dataType: "json", // Asegurarse de recibir JSON
                                        success: function(respuesta) {
                                            if (respuesta && respuesta.success) {
                                                $("#mensajeExito").text(respuesta.message).show();
                                                setTimeout(function() {
                                                    window.location.href = "lista_productos.php";
                                                }, 2000);
                                            } else {
                                                alert(respuesta ? respuesta.message : "Error inesperado.");
                                            }
                                        },
                                        error: function() {
                                            alert("Ocurrió un error al eliminar el producto.");
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


<?php include('../layout/parte2.php'); ?>