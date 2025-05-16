<?php
include_once '../app/config.php';
include_once '../layout/sesion.php';
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
                            <h5 class="card-title">Lista de Recibos</h5>

                            <div style="overflow-x:auto;">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar clientes..." onkeyup="searchFunction()">
                                <table id="reciboTable" class="table table-striped table-bordered" style="width:100%; font-size: 0.9em;">
                                    <thead>
                                        <tr>
                                            <th>N° Recibo</th>

                                            <th>Cliente</th>
                                            <th>Fecha Emisión</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Fecha Envio SUNAT</th>
                                            <th>Monto Total</th>
                                            <th>Descargar XML</th>
                                            <th>Descargar PDF</th>
                                            <th>Descargar TICKET</th>
                                            <th>Estado de pago Cliente</th>


                                            <th>Estado SUNAT</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $pdo->prepare("SELECT r.id_recibo, r.numero_recibo, r.Tipo_documento, r.dni_ruc, r.id_cliente, r.id_emisor, r.id_plan_servicio, r.fecha_emision, r.fecha_vencimiento, r.monto_unitario, r.descuento, r.monto_total, r.estado, r.estado_sunat, r.fecha_envio_sunat FROM recibos r ORDER BY r.fecha_emision DESC");
                                        $stmt->execute();
                                        $recibos = $stmt->fetchAll();
                                        $totalRecibos = count($recibos);
                                        $recibosPorPagina = 5;
                                        $totalPaginas = ceil($totalRecibos / $recibosPorPagina);
                                        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                        $inicio = ($paginaActual - 1) * $recibosPorPagina;
                                        $recibosPagina = array_slice($recibos, $inicio, $recibosPorPagina);

                                        foreach ($recibosPagina as $row) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['numero_recibo']; ?></td>

                                                <td>
                                                    <?php
                                                    $stmt2 = $pdo->prepare("SELECT nombre FROM clientes WHERE id_cliente = :id_cliente");
                                                    $stmt2->execute(['id_cliente' => $row['id_cliente']]);
                                                    $fila2 = $stmt2->fetch();
                                                    echo $fila2['nombre'] . ' - ' . $row['dni_ruc'];
                                                    ?>
                                                </td>
                                                <td><?php echo date('d-m-Y', strtotime($row['fecha_emision'])); ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($row['fecha_vencimiento'])); ?></td>

                                                <td><?php echo (is_null($row['fecha_envio_sunat'])) ? 'Aun no ha sido enviado' : date('d-m-Y H:i:s', strtotime($row['fecha_envio_sunat'])); ?></td>

                                                <td><?php echo $row['monto_total']; ?></td>

                                                <td>
                                                    <a href="descargar_xml.php?id_recibo=<?php echo $row['id_recibo']; ?>" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-file-download"></i> XML
                                                    </a>
                                                </td>

                                                <td>
                                                    <a href="descargar_pdf.php?id_recibo=<?php echo $row['id_recibo']; ?>" class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-file-pdf"></i> PDF
                                                    </a>

                                                </td>

                                                <td>
                                                    <a href="descargar_tiket.php?id_recibo=<?php echo $row['id_recibo']; ?>" class="btn btn-outline-warning btn-sm">
                                                        <i class="fas fa-file-pdf"></i> TIKET
                                                    </a>

                                                </td>
                                                <td id="fila<?php echo $row['id_recibo']; ?>">
                                                    <!-- Span para el estado -->
                                                    <span id="estadoSpan<?php echo $row['id_recibo']; ?>" class="badge 
    <?php
                                            switch ($row['estado']) {
                                                case 'NO_ENVIADO':
                                                    echo 'badge-secondary';
                                                    break;
                                                case 'ENVIADO':
                                                    echo 'badge-info';
                                                    break;
                                                case 'VENCIDO':
                                                    echo 'badge-danger';
                                                    break;
                                                case 'PAGADO':
                                                    echo 'badge-success';
                                                    break;
                                                default:
                                                    echo 'badge-secondary';
                                                    break;
                                            }
    ?>">
                                                        <?php echo $row['estado']; ?>
                                                    </span>



                                                    <!-- Botón para abrir el modal -->
                                                    <button id="botonCambiarEstado<?php echo $row['id_recibo']; ?>" type="button" class="btn btn-outline-warning btn-sm" data-toggle="modal" data-target="#modalCambiarEstado<?php echo $row['id_recibo']; ?>">
                                                        <i class="fas fa-edit"></i> Cambiar Estado
                                                    </button>




                                                    <div id="recibo<?php echo $row['id_recibo']; ?>" style="display:<?php echo $row['estado'] === 'PAGADO' ? '' : 'none'; ?>">
                                                        <a href="descargar_recibo_pagado.php?id_recibo=<?php echo $row['id_recibo']; ?>" class="btn btn-outline-success btn-sm">
                                                            <i class="fas fa-download"></i> Descargar Recibo
                                                        </a>
                                                    </div>


                                                    <div class="modal fade" id="modalCambiarEstado<?php echo $row['id_recibo']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">Cambiar estado del recibo de <?php echo $fila2['nombre'] . ' - ' . $row['dni_ruc']; ?></h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <!-- Agregamos un id único para el formulario -->
                                                                <form id="formCambiarEstado<?php echo $row['id_recibo']; ?>">
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label for="estado">Estado</label>
                                                                            <select class="form-control" id="estado" name="estado" required>
                                                                                <option value="NO_ENVIADO" <?php echo $row['estado'] == 'NO_ENVIADO' ? 'selected' : ''; ?>>No Enviado</option>
                                                                                <option value="ENVIADO" <?php echo $row['estado'] == 'ENVIADO' ? 'selected' : ''; ?>>Enviado</option>
                                                                                <option value="VENCIDO" <?php echo $row['estado'] == 'VENCIDO' ? 'selected' : ''; ?>>Vencido</option>
                                                                                <option value="PAGADO" <?php echo $row['estado'] == 'PAGADO' ? 'selected' : ''; ?>>Pagado</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group" id="select_pago" style="display:<?php echo $row['estado'] === 'PAGADO' ? '' : 'none'; ?>">
                                                                            <label for="pago">Pago</label>
                                                                            <select class="form-control" id="pago" name="pago" required>
                                                                                <option value="Yape">Yape</option>
                                                                                <option value="Efectivo">Efectivo</option>
                                                                                <option value="Transferencia">Transferencia</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group" id="descripcion_pago" style="display:<?php echo $row['estado'] === 'PAGADO' ? '' : 'none'; ?>">
                                                                            <label for="descripcion_pago">Descripci n del pago</label>
                                                                            <textarea class="form-control" id="descripcion_pago" name="descripcion_pago" rows="3" required></textarea>
                                                                        </div>
                                                                        <div class="form-group" id="evidencia_pago" style="display:<?php echo $row['estado'] === 'PAGADO' ? '' : 'none'; ?>">
                                                                            <label for="evidencia_pago">Evidencia del pago (opcional)</label>
                                                                            <input type="file" class="form-control-file" id="evidencia_pago" name="evidencia_pago" accept=".png, .jpg, .jpeg, .pdf">
                                                                        </div>


                                                                        <script>
                                                                            // Listen for changes in the 'estado' select element
                                                                            /* document.getElementById('estado').addEventListener('change', function() {
                                                                                const select_pago = document.getElementById('select_pago');
                                                                                const descripcion_pago = document.getElementById('descripcion_pago');
                                                                                const evidencia_pago = document.getElementById('evidencia_pago');
                                                                                if (this.value === 'PAGADO') {
                                                                                    select_pago.style.display = '';
                                                                                    descripcion_pago.style.display = '';
                                                                                    evidencia_pago.style.display = '';
                                                                                } else {
                                                                                    select_pago.style.display = 'none';
                                                                                    descripcion_pago.style.display = 'none';
                                                                                    evidencia_pago.style.display = 'none';
                                                                                }
                                                                            }); */
                                                                        </script>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                                        <!-- Cambiamos el botón de tipo submit -->
                                                                        <button type="button" class="btn btn-primary" onclick="guardarEstado(<?php echo $row['id_recibo']; ?>)">Guardar</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>








                                                <td>
                                                    <?php
                                                    if ($row['estado_sunat'] == 'ACEPTADA') {
                                                        echo '<span class="badge badge-success">ACEPTADA</span>';
                                                    } else if ($row['estado_sunat'] == 'RECHAZADA') {
                                                        echo '<span class="badge badge-danger">RECHAZADA</span>';
                                                    } else if ($row['estado_sunat'] == 'NO_ENVIADA') {
                                                        echo '<span class="badge badge-warning">NO_ENVIADA</span>';
                                                    } else {
                                                        echo '<span class="badge badge-secondary">PENDIENTE</span>';
                                                    }
                                                    ?>



                                                    <?php if ($row['estado_sunat'] == 'NO_ENVIADA') { ?>
                                                        <a href="enviar_sunat.php?id_recibo=<?php echo $row['id_recibo']; ?>" class="btn btn-outline-primary btn-sm ml-2">Enviar a SUNAT</a>
                                                    <?php } ?>
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
                                    table = document.getElementById("reciboTable");
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

                                function guardarEstado(idRecibo) {
                                    const formId = `formCambiarEstado${idRecibo}`;
                                    const form = document.getElementById(formId);

                                    if (!form) {
                                        console.error(`No se encontró el formulario con ID ${formId}`);
                                        alert('Hubo un problema al encontrar el formulario.');
                                        return;
                                    }

                                    const formData = new FormData(form);
                                    formData.append('id_recibo', idRecibo);

                                    console.log('Enviando datos:', idRecibo, formData.get('estado'));

                                    fetch('cambiar_estado_cliente.php', {
                                            method: 'POST',
                                            body: formData,
                                        })
                                        .then((response) => response.json())
                                        .then((data) => {
                                            console.log('Respuesta del servidor:', data);

                                            if (data.success) {
                                                // Actualizar la fila correspondiente en la tabla
                                                const fila = document.querySelector(`#fila${idRecibo}`);
                                                if (fila) {
                                                    console.log('Actualizando fila con ID:', idRecibo);

                                                    // Actualizar el span del estado
                                                    const estadoSpan = fila.querySelector(`#estadoSpan${idRecibo}`);
                                                    if (estadoSpan) {
                                                        estadoSpan.textContent = data.estadoactualizado;
                                                        estadoSpan.className = `badge badge-${estadoClase(data.estadoactualizado)}`; // Actualizar clase del badge
                                                    } else {
                                                        console.error('No se encontró el elemento con ID estadoSpan' + idRecibo);
                                                    }

                                                    // Mostrar o ocultar el botón para descargar recibo si el estado es pagado
                                                    const botonDescargar = fila.querySelector(`#recibo${idRecibo}`);
                                                    if (botonDescargar) {
                                                        botonDescargar.style.display = data.estadoactualizado === 'PAGADO' ? 'block' : 'none';
                                                    } else {
                                                        console.error('No se encontró el elemento con ID recibo' + idRecibo);
                                                    }

                                                    // Actualizar el estado del botón cambiar estado
                                                    const botonCambiarEstado = fila.querySelector(`#botonCambiarEstado${idRecibo}`);
                                                    if (botonCambiarEstado) {
                                                        botonCambiarEstado.style.display = data.estadoactualizado === 'PAGADO' ? 'none' : 'block';
                                                    } else {
                                                        console.error('No se encontró el elemento con ID botonCambiarEstado' + idRecibo);
                                                    }

                                                } else {
                                                    console.error(`No se encontró la fila con ID fila${idRecibo}`);
                                                }

                                                // Cerrar la modal
                                                const modalId = `#modalCambiarEstado${idRecibo}`;
                                                const modal = document.querySelector(modalId);
                                                if (modal) {
                                                    $(modal).modal('hide');
                                                } else {
                                                    console.error(`No se encontró la modal con ID ${modalId}`);
                                                }

                                            } else {
                                                alert('Error al actualizar el estado: ' + (data.message || 'Respuesta desconocida del servidor.'));
                                            }
                                        })
                                        .catch((error) => {
                                            console.error('Error en la solicitud:', error);
                                            alert('Hubo un error al procesar la solicitud. Intenta nuevamente.');
                                        });
                                }

                                function estadoClase(estado) {
                                    switch (estado) {
                                        case 'NO_ENVIADO':
                                            return 'secondary';
                                        case 'ENVIADO':
                                            return 'info';
                                        case 'VENCIDO':
                                            return 'danger';
                                        case 'PAGADO':
                                            return 'success';
                                        default:
                                            return 'secondary';
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




<?php include('../layout/parte2.php'); ?>