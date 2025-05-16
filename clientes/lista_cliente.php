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
                            <h5 class="card-title">Lista de Clientes</h5>

                            <div style="overflow-x:auto;">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar clientes..." onkeyup="searchFunction()">
                                <table id="clientes" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>DNI/RUC</th>
                                            <!-- <th>Dirección</th>
                                            <th>Teléfono</th> -->
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $pdo->prepare("SELECT id_cliente, nombre, apellido_paterno, apellido_materno, dni_ruc, tipo_documento, celular, email, direccion, fecha_registro, estado FROM clientes WHERE estado != 2");
                                        $stmt->execute();
                                        $clientes = $stmt->fetchAll();
                                        $totalClientes = count($clientes);
                                        $clientesPorPagina = 5;
                                        $totalPaginas = ceil($totalClientes / $clientesPorPagina);
                                        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                        $inicio = ($paginaActual - 1) * $clientesPorPagina;
                                        $clientesPagina = array_slice($clientes, $inicio, $clientesPorPagina);

                                        foreach ($clientesPagina as $row) {
                                        ?>
                                            <tr>
                                                <td><?php echo $inicio + array_search($row, $clientesPagina) + 1; ?></td>
                                                <td><?php echo $row['nombre']; ?></td>
                                                <td><?php echo $row['apellido_paterno'] . ' ' . $row['apellido_materno']; ?></td>
                                                <td><?php echo $row['dni_ruc']; ?></td>
                                                <!-- <td><?php echo $row['direccion']; ?></td>
                                                <td><?php echo $row['celular']; ?></td> -->
                                                <td>
                                                    <?php
                                                    if ($row['estado'] == 1) {
                                                        echo '<span class="badge badge-success">Activo</span>';
                                                    } else {
                                                        echo '<span class="badge badge-danger">Inactivo</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group-vertical btn-group-sm d-flex justify-content-center">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Acciones
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="detalle_cliente.php?id=<?php echo $row['id_cliente']; ?>">Más detalles</a>
                                                            <a class="dropdown-item" href="editar_cliente.php?id=<?php echo $row['id_cliente']; ?>">Editar</a>
                                                            
                                                            <a class="dropdown-item" href="eliminar_cliente.php?id=<?php echo $row['id_cliente']; ?>">Eliminar</a>
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
                                    table = document.getElementById("clientes");
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