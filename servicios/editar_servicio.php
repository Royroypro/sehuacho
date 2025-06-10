<?php
include_once '../app/config.php';
include_once '../app/controllers/servicios/consultar_servicio.php';
$id = $_GET['id'] ?? null;

$nombre = $descripcion = $precio = $unidad_medida = $codigo = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT Nombre, Descripcion, Precio, UnidadMedida, Codigo FROM servicios WHERE ServicioID = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($servicio) {
        $nombre = htmlspecialchars($servicio['Nombre'], ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars($servicio['Descripcion'], ENT_QUOTES, 'UTF-8');
        $precio = htmlspecialchars($servicio['Precio'], ENT_QUOTES, 'UTF-8');
        $unidad_medida = htmlspecialchars($servicio['UnidadMedida'], ENT_QUOTES, 'UTF-8');
        $codigo = htmlspecialchars($servicio['Codigo'], ENT_QUOTES, 'UTF-8');
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
                        <h4 class="card-title">Editar Servicio</h4>
                        <h6 class="card-subtitle">Complete la información</h6>

                        <form id="editarServicioForm" method="POST" class="form-horizontal m-t-30">
                            <input type="hidden" name="ServicioID" id="ServicioID" value="<?= $id ?>">

                            <div class="form-group">
                                <label for="nombre" class="col-sm-12">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="nombre" name="Nombre" placeholder="Ingrese el nombre del servicio" value="<?= $nombre ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion" class="col-sm-12">Descripción</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="descripcion" name="Descripcion" placeholder="Ingrese una descripción del servicio" required><?= $descripcion ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="precio" class="col-sm-12">Precio S/.</label>
                                <div class="col-sm-12">
                                    <input type="number" step="0.01" class="form-control" id="precio" name="Precio" placeholder="Ingrese el precio del servicio" value="<?= $precio ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="unidad_medida" class="col-sm-12">Unidad de Medida</label>
                                <div class="col-sm-12">
                                    <select class="form-control" id="unidad_medida" name="UnidadMedida" required>
                                        <option value="NIU" <?= $unidad_medida === 'NIU' ? 'selected' : '' ?>>UNIDAD</option>
                                        <option value="HORA" <?= $unidad_medida === 'HORA' ? 'selected' : '' ?>>HORA</option>
                                        <option value="DÍA" <?= $unidad_medida === 'DÍA' ? 'selected' : '' ?>>DÍA</option>
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

                                $('#editarServicioForm').on('submit', function(e) {
                                    e.preventDefault();

                                    const datos = {
                                        ServicioID: $('#ServicioID').val().trim(),
                                        Nombre: nombreInput.val().trim(),
                                        Descripcion: $('#descripcion').val().trim(),
                                        UnidadMedida: $('#unidad_medida').val(),
                                        Precio: $('#precio').val().trim(),
                                        Codigo: codigoInput.val().trim()
                                    };

                                    if (!datos.Codigo) {
                                        alert('Por favor, complete el campo de código.');
                                        return;
                                    }

                                    $.ajax({
                                        url: '../app/controllers/servicios/editar_servicio.php',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: datos,
                                        success: function(res) {
                                            if (res.success) {
                                                $('#mensajeExito').text(res.message || 'Servicio actualizado correctamente').show();
                                                setTimeout(function() {
                                                    window.location.href = "lista_servicios.php";
                                                }, 1000);
                                            } else {
                                                alert(res.message || 'Error al actualizar el servicio.');
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
