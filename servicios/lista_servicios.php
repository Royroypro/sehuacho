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
                            <h5 class="card-title">Lista de Servicios</h5>

                            <div style="overflow-x:auto;">
                                <input
                                    type="text"
                                    id="searchInput"
                                    class="form-control"
                                    placeholder="Buscar servicios..."
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
                                            <th>Código</th>
                                            <th>Precio</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Obtener todos los servicios
                                        $stmt = $pdo->prepare("SELECT * FROM servicios WHERE estado = 1");
                                        $stmt->execute();
                                        $servicios = $stmt->fetchAll();

                                        // Paginación
                                        $totalItems     = count($servicios);
                                        $itemsPorPagina = 5;
                                        $totalPaginas   = ceil($totalItems / $itemsPorPagina);
                                        $paginaActual   = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                        $inicio         = ($paginaActual - 1) * $itemsPorPagina;
                                        $paginaItems    = array_slice($servicios, $inicio, $itemsPorPagina);

                                        foreach ($paginaItems as $index => $row) {
                                            $numero   = $inicio + $index + 1;
                                            $idServ   = $row['ServicioID'];
                                        ?>
                                            <tr>
                                                <td><?= $numero; ?></td>
                                                <td><?= htmlspecialchars($row['Nombre'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($row['Descripcion'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($row['codigo'] ?? '-'); ?></td>
                                                <td><?= $row['Precio'] !== null ? number_format($row['Precio'], 2) : '-'; ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-secondary dropdown-toggle"
                                                            type="button"
                                                            id="accionesDropdown<?= $idServ; ?>"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false"
                                                        >
                                                            Acciones
                                                        </button>
                                                        <div
                                                            class="dropdown-menu"
                                                            aria-labelledby="accionesDropdown<?= $idServ; ?>"
                                                        >
                                                            <a
                                                                class="dropdown-item"
                                                                href="editar_servicio.php?id=<?= $idServ; ?>"
                                                            >Editar</a>
                                                            <a
                                                                class="dropdown-item"
                                                                href="eliminar_servicio.php?id=<?= $idServ; ?>"
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
