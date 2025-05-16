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
                            <h5 class="card-title">Lista de Cotizaciones</h5>

                            <div style="overflow-x:auto;">
                                <input
                                    type="text"
                                    id="searchInput"
                                    class="form-control"
                                    placeholder="Buscar cotizaciones..."
                                    onkeyup="searchFunction()"
                                >
                                <table
                                    id="cotizaciones"
                                    class="table table-striped table-bordered"
                                    style="width:100%"
                                >
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Cotización</th>
                                            <th>Cliente</th>
                                            <th>Emisión</th>
                                            <th>Validez</th>
                                            <th>Subtotal</th>
                                            <th>Impuestos</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Obtener cotizaciones activas
                                        $stmt = $pdo->prepare(
                                            "SELECT c.CotizacionID,
                                                    c.FechaCotizacion,
                                                    c.FechaValidez,
                                                    c.Subtotal,
                                                    c.Impuestos,
                                                    c.Total,
                                                    c.Estado,
                                                    cl.nombre AS Cliente,
                                                    imp.Nombre AS ImpuestoNombre
                                             FROM cotizaciones c
                                             LEFT JOIN clientes cl ON c.ClienteID = cl.id_cliente
                                             LEFT JOIN impuestos imp ON c.ImpuestoID = imp.ImpuestoID
                                             WHERE c.Estado IN ('Pendiente','Activa')"
                                        );
                                        $stmt->execute();
                                        $cotizaciones = $stmt->fetchAll();

                                        // Paginación
                                        $totalItems     = count($cotizaciones);
                                        $itemsPorPagina = 5;
                                        $totalPaginas   = ceil($totalItems / $itemsPorPagina);
                                        $paginaActual   = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                        $inicio         = ($paginaActual - 1) * $itemsPorPagina;
                                        $paginaItems    = array_slice($cotizaciones, $inicio, $itemsPorPagina);

                                        foreach ($paginaItems as $index => $row) {
                                            $numero         = $inicio + $index + 1;
                                            $idCot          = $row['CotizacionID'];
                                            $cliente        = htmlspecialchars($row['Cliente']);
                                            $fechaEmision   = date('d/m/Y', strtotime($row['FechaCotizacion']));
                                            $fechaValidez   = $row['FechaValidez'] ? date('d/m/Y', strtotime($row['FechaValidez'])) : '-';
                                            $subtotal       = number_format($row['Subtotal'], 2);
                                            $impuestos      = number_format($row['Impuestos'], 2) . ' (' . htmlspecialchars($row['ImpuestoNombre']) . ')';
                                            $total          = number_format($row['Total'], 2);
                                            $estadoTexto    = htmlspecialchars($row['Estado']);
                                        ?>
                                            <tr>
                                                <td><?= $numero; ?></td>
                                                <td><?= $idCot; ?></td>
                                                <td><?= $cliente; ?></td>
                                                <td><?= $fechaEmision; ?></td>
                                                <td><?= $fechaValidez; ?></td>
                                                <td><?= $subtotal; ?></td>
                                                <td><?= $impuestos; ?></td>
                                                <td><?= $total; ?></td>
                                                <td><?= $estadoTexto; ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-secondary dropdown-toggle"
                                                            type="button"
                                                            id="accionesDropdown<?= $idCot; ?>"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false"
                                                        >
                                                            Acciones
                                                        </button>
                                                        <div
                                                            class="dropdown-menu"
                                                            aria-labelledby="accionesDropdown<?= $idCot; ?>"
                                                        >
                                                            <a class="dropdown-item" href="ver_cotizacion.php?id=<?= $idCot; ?>">Ver</a>
                                                            <a class="dropdown-item" href="editar_cotizacion.php?id=<?= $idCot; ?>">Editar</a>
                                                            <a class="dropdown-item" href="eliminar_cotizacion.php?id=<?= $idCot; ?>">Eliminar</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <div class="pagination">
                                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                        <a href="?pagina=<?= $i; ?>" class="btn btn-primary <?= $i == $paginaActual ? 'active' : ''; ?>"><?= $i; ?></a>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <script>
                                function searchFunction() {
                                    var input = document.getElementById("searchInput");
                                    var filter = input.value.toUpperCase();
                                    var table = document.getElementById("cotizaciones");
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
