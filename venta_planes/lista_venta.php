<?php
include_once '../app/config.php';
?>
<div id="main-wrapper">
    <?php

    include_once '../layout/parte1.php';
    ?>

    
    <div class="page-wrapper">
    
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        
                        <div class="card-body">
                            <h5 class="card-title">Lista de Venta de Planes</h5>

                            <div style="overflow-x:auto;">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar planes..." onkeyup="searchFunction()">
                                <table id="planes" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Cliente</th>
                                            <th>Ip</th>
                                            <th>Ubicacion</th>
                                            <th>Plan</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $pdo->prepare("SELECT id, id_cliente, id_planes_servicios, Ip, Ubicacion, Fecha_inicio, Fecha_finalizacion, Estado FROM cliente_planes WHERE estado != 2");
                                        $stmt->execute();
                                        $planes = $stmt->fetchAll();
                                        $totalPlanes = count($planes);
                                        $planesPorPagina = 5;
                                        $totalPaginas = ceil($totalPlanes / $planesPorPagina);
                                        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                        $inicio = ($paginaActual - 1) * $planesPorPagina;
                                        $planesPagina = array_slice($planes, $inicio, $planesPorPagina);

                                        foreach ($planesPagina as $row) {
                                            $stmt_cliente = $pdo->prepare("SELECT nombre FROM clientes WHERE id_cliente = :id_cliente");
                                            $stmt_cliente->bindParam(':id_cliente', $row['id_cliente']);
                                            $stmt_cliente->execute();
                                            $fila_cliente = $stmt_cliente->fetch();
                                            $nombre_cliente = $fila_cliente["nombre"] ?? null;
                                        ?>
                                            <tr>
                                                <td><?php echo $inicio + array_search($row, $planesPagina) + 1; ?></td>
                                                <td><?php echo $nombre_cliente; ?></td>
                                                <td><?php echo $row['Ip']; ?></td>

                                                <td><?php echo $row['Ubicacion']; ?></td>
                                                <td>
                                                    <?php
                                                    $stmt_plan = $pdo->prepare("SELECT nombre_plan, tarifa_mensual, velocidad, igv_tarifa FROM planes_servicio WHERE id_plan_servicio = :id_plan_servicio");
                                                    $stmt_plan->bindParam(':id_plan_servicio', $row['id_planes_servicios']);
                                                    $stmt_plan->execute();
                                                    $fila_plan = $stmt_plan->fetch();
                                                    $tarifa_mensual_igv = $fila_plan['tarifa_mensual'] + $fila_plan['igv_tarifa'];
                                                    echo $fila_plan['nombre_plan'] . " - S/." . $tarifa_mensual_igv . " - Velocidad: " . $fila_plan['velocidad'] . "MB";
                                                    ?>
                                                </td>
                                                
                                                <td>
                                                    <?php
                                                    if ($row['Estado'] == 1) {
                                                        echo '<span class="badge badge-success">Activo</span>';
                                                    } else {
                                                        echo '<span class="badge badge-danger">Inactivo</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Acciones
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item" href="editar_venta.php?id=<?php echo $row['id']; ?>">Editar</a>
                                                            <a class="dropdown-item" href="eliminar_venta.php?id=<?php echo $row['id']; ?>">Eliminar</a>
                                                            <a class="dropdown-item" href="detalle_venta.php?id=<?php echo $row['id']; ?>">Detalles</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <div class="pagination">
                                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                        <a href="?pagina=<?php echo $i; ?>" class="btn btn-primary <?php echo $i == $paginaActual ? 'active' : ''; ?>"><?php echo $i; ?></a>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <script>
                                function searchFunction() {
                                    var input, filter, table, tr, td, i, txtValue;
                                    input = document.getElementById("searchInput");
                                    filter = input.value.toUpperCase();
                                    table = document.getElementById("planes");
                                    tr = table.getElementsByTagName("tr");
                                    for (i = 1; i < tr.length; i++) {
                                        tr[i].style.display = "none";
                                        td = tr[i].getElementsByTagName("td");
                                        for (var j = 0; j < td.length; j++) {
                                            if (td[j]) {
                                                txtValue = td[j].textContent || td[j].innerText;
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




<?php include('../layout/parte2.php');?>