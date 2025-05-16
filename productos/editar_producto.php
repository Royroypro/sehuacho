<?php
include_once '../app/config.php';
include_once '../app/controllers/productos/consultar_producto.php';
$id = $_GET['id'] ?? null;

$nombre = $descripcion = $precio_unitario = $unidad_medida = $codigo = $categoria_id = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT Nombre, Descripcion, PrecioUnitario, UnidadMedida, Codigo, CategoriaID FROM productos WHERE ProductoID = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        $nombre = htmlspecialchars($producto['Nombre'], ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars($producto['Descripcion'], ENT_QUOTES, 'UTF-8');
        $precio_unitario = htmlspecialchars($producto['PrecioUnitario'], ENT_QUOTES, 'UTF-8');
        $unidad_medida = htmlspecialchars($producto['UnidadMedida'], ENT_QUOTES, 'UTF-8');
        $codigo = htmlspecialchars($producto['Codigo'], ENT_QUOTES, 'UTF-8');
        $categoria_id = htmlspecialchars($producto['CategoriaID'], ENT_QUOTES, 'UTF-8');
    }
}
?>

<div id="main-wrapper">
    <?php
    include_once '../layout/parte1.php';

    ?>
    <div class="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Editar Producto</h4>
                        <h6 class="card-subtitle">Complete la información</h6>

                        <form id="editarProductoForm" method="POST" class="form-horizontal m-t-30">
                            <input type="hidden" name="ProductoID" id="ProductoID" value="<?= $id ?>">

                            <div class="form-group">
                                <label for="nombre" class="col-sm-12">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="nombre" name="Nombre" placeholder="Ingrese el nombre del producto" value="<?= $nombre ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion" class="col-sm-12">Descripción</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="descripcion" name="Descripcion" placeholder="Ingrese una descripción del producto" required><?= $descripcion ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="precio_unitario" class="col-sm-12">Precio Unitario S/.</label>
                                <div class="col-sm-12">
                                    <input type="number" step="0.01" class="form-control" id="precio_unitario" name="PrecioUnitario" placeholder="Ingrese el precio unitario del producto" value="<?= $precio_unitario ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="unidad_medida" class="col-sm-12">Unidad de Medida</label>
                                <div class="col-sm-12">
                                    <select class="form-control" id="unidad_medida" name="UnidadMedida" required>
                                        <option value="NIU" <?= $unidad_medida === 'NIU' ? 'selected' : '' ?>>UNIDAD</option>
                                        <option value="KG" <?= $unidad_medida === 'KG' ? 'selected' : '' ?>>KILOGRAMO</option>
                                        <option value="M" <?= $unidad_medida === 'M' ? 'selected' : '' ?>>METRO</option>
                                        <option value="LTS" <?= $unidad_medida === 'LTS' ? 'selected' : '' ?>>LITRO</option>
                                        <option value="ROLLO" <?= $unidad_medida === 'ROLLO' ? 'selected' : '' ?>>ROLLO</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="codigo" class="col-sm-12">Código</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="codigo" name="Codigo" placeholder="El código se generará automáticamente" value="<?= $codigo ?>" readonly required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="categoria" class="col-sm-12">Categoría</label>
                                <div class="col-sm-12">
                                    <select class="form-control" id="categoria" name="CategoriaID" required>
                                        <?php
                                        $stmt = $pdo->query("SELECT CategoriaID, Nombre FROM categorias");
                                        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($categorias as $categoria) {
                                            $cat_id = htmlspecialchars($categoria['CategoriaID'], ENT_QUOTES, 'UTF-8');
                                            $cat_nombre = htmlspecialchars($categoria['Nombre'], ENT_QUOTES, 'UTF-8');
                                            $selected = $categoria_id == $cat_id ? 'selected' : '';
                                            echo "<option value='$cat_id' $selected>$cat_nombre</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-warning" style="background-color: #EC672B; border-color: #EC672B;">Actualizar</button>
                                </div>
                            </div>
                        </form>

                        <div id="mensajeExito" style="display:none; color:green; font-weight:bold;"></div>

                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            $(document).ready(function() {
                                const codigoInput = $('#codigo');
                                const nombreInput = $('#nombre');

                                // Verificar si el campo 'codigo' ya tiene un valor al cargar la página
                                if (!codigoInput.val()) {
                                    nombreInput.on('input', function() {
                                        const nombre = $(this).val().trim();
                                        if (nombre.length >= 2) {
                                            const letras = nombre.substring(0, 2).toUpperCase();
                                            const numero = Math.floor(10000000 + Math.random() * 90000000);
                                            codigoInput.val(`${letras}${numero}`);
                                        } else {
                                            codigoInput.val('');
                                        }
                                    });
                                }

                                $('#editarProductoForm').on('submit', function(e) {
                                    e.preventDefault();

                                    const datos = {
                                        ProductoID: $('#ProductoID').val().trim(),
                                        Nombre: nombreInput.val().trim(),
                                        Descripcion: $('#descripcion').val().trim(),
                                        UnidadMedida: $('#unidad_medida').val(),
                                        PrecioUnitario: $('#precio_unitario').val().trim(),
                                        Codigo: codigoInput.val().trim(),
                                        CategoriaID: $('#categoria').val().trim()
                                    };

                                    for (let campo in datos) {
                                        if (!datos[campo]) {
                                            alert('Por favor, complete todos los campos.');
                                            return;
                                        }
                                    }

                                    $.ajax({
                                        url: '../app/controllers/productos/editar_producto.php',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: datos,
                                        success: function(res) {
                                            if (res.success) {
                                                $('#mensajeExito').text(res.message || 'Producto actualizado correctamente').show();
                                                setTimeout(function() {
                                                    window.location.href = "lista_productos.php";
                                                }, 1000);
                                            } else {
                                                alert(res.message || 'Error al actualizar el producto.');
                                            }
                                        },
                                        error: function(xhr, status, err) {
                                            console.error('AJAX Error:', status, err);
                                            alert('Ocurrió un error. Intenta nuevamente.');
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