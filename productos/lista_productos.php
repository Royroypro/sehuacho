<?php
include_once '../app/config.php';
?>
<div id="main-wrapper">
    <?php include_once '../layout/parte1.php'; ?>

    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Lista de Productos</h5>

                            <div style="overflow-x:auto;">
                                <input
                                    type="text"
                                    id="searchInput"
                                    class="form-control"
                                    placeholder="Buscar productos..."
                                    onkeyup="searchFunction()"
                                >
                                <table
                                    id="productos"
                                    class="table table-striped table-bordered"
                                    style="width:100%"
                                >
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Unidad de Medida</th>
                                            <th>Precio Unitario</th>
                                            <th>Código</th>
                                            <th>Categoría</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Obtener todos los productos
                                        $stmt = $pdo->prepare("SELECT * FROM productos");
                                        $stmt->execute();
                                        $productos = $stmt->fetchAll();

                                        // Paginación
                                        $totalItems     = count($productos);
                                        $itemsPorPagina = 5;
                                        $totalPaginas   = ceil($totalItems / $itemsPorPagina);
                                        $paginaActual   = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                        $inicio         = ($paginaActual - 1) * $itemsPorPagina;
                                        $paginaItems    = array_slice($productos, $inicio, $itemsPorPagina);

                                        $stmt = $pdo->prepare("SELECT * FROM productos WHERE Estado = 1");
                                        $stmt->execute();
                                        $paginaItems = $stmt->fetchAll();

                                        foreach ($paginaItems as $index => $row) {
                                            $numero   = $inicio + $index + 1;
                                            $idProd   = $row['ProductoID'];
                                            $stmt2 = $pdo->prepare("SELECT Nombre FROM categorias WHERE CategoriaID = :id");
                                            $stmt2->bindParam(':id', $row['CategoriaID']);
                                            $stmt2->execute();
                                            $categoria = $stmt2->fetchColumn() ?? '-';
                                        ?>
                                            <tr>
                                                <td><?= $numero; ?></td>
                                                <td><?= htmlspecialchars($row['Nombre'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($row['Descripcion'] ?? '-'); ?></td>
                                                <td><?= $row['UnidadMedida'] === 'NIU' ? 'UNIDAD' : htmlspecialchars($row['UnidadMedida'] ?? '-'); ?></td>
                                                <td><?= $row['PrecioUnitario'] !== null ? number_format($row['PrecioUnitario'], 2) : '-'; ?></td>
                                                <td><?= htmlspecialchars($row['Codigo'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($categoria ?? '-'); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-secondary dropdown-toggle"
                                                            type="button"
                                                            id="accionesDropdown<?= $idProd; ?>"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false"
                                                        >
                                                            Acciones
                                                        </button>
                                                        <div
                                                            class="dropdown-menu"
                                                            aria-labelledby="accionesDropdown<?= $idProd; ?>"
                                                        >
                                                            <a
                                                                class="dropdown-item"
                                                                href="editar_producto.php?id=<?= $idProd; ?>"
                                                            >Editar</a>
                                                            <a
                                                                class="dropdown-item"
                                                                href="eliminar_producto.php?id=<?= $idProd; ?>"
                                                            >Eliminar</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <div class="pagination">
                                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                        <a
                                            href="?pagina=<?= $i; ?>"
                                            class="btn btn-primary <?= $i == $paginaActual ? 'active' : ''; ?>"
                                        ><?= $i; ?></a>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <script>
                                function searchFunction() {
                                    var input = document.getElementById("searchInput");
                                    var filter = input.value.toUpperCase();
                                    var table = document.getElementById("productos");
                                    var tr = table.getElementsByTagName("tr");
                                    for (var i = 1; i < tr.length; i++) {
                                        tr[i].style.display = "none";
                                        var td = tr[i].getElementsByTagName("td");
                                        for (var j = 0; j < td.length; j++) {
                                            if (td[j]) {
                                                var txtValue = td[j].textContent || td[j].innerText;
                                                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                                    tr[i].style.display = "";
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            </script>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../layout/parte2.php'; ?>