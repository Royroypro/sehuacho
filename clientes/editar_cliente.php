<?php
include_once '../app/config.php';
include_once '../app/controllers/clientes/consultar_cliente.php';
?>
<div id="main-wrapper">
    <?php

    include_once '../layout/parte1.php';

    $id_cliente = isset($_GET['id']) ? $_GET['id'] : null;
    ?>

    <div class="page-wrapper">
        <?php
  
        if ($cliente) {
            $nombre = htmlspecialchars($cliente['nombre'], ENT_QUOTES, 'UTF-8');
            $apellido_paterno = htmlspecialchars($cliente['apellido_paterno'], ENT_QUOTES, 'UTF-8');
            $apellido_materno = htmlspecialchars($cliente['apellido_materno'], ENT_QUOTES, 'UTF-8');
            $dni_ruc = htmlspecialchars($cliente['dni_ruc'], ENT_QUOTES, 'UTF-8');
            $tipo_documento = htmlspecialchars($cliente['tipo_documento'], ENT_QUOTES, 'UTF-8');
            $celular = htmlspecialchars($cliente['celular'], ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8');
            $direccion = htmlspecialchars($cliente['direccion'], ENT_QUOTES, 'UTF-8');
            $referencia = htmlspecialchars($cliente['referencia'], ENT_QUOTES, 'UTF-8');
            $estado = htmlspecialchars($cliente['estado'], ENT_QUOTES, 'UTF-8');
        }
    
    
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Editar Cliente</h4>
                        <h6 class="card-subtitle">Complete la información</h6>
                        <!-- <form  class="form-horizontal m-t-30" action="../app/controllers/clientes/editar_cliente.php" method="POST"> -->
                        <form id="editarClienteForm" class="form-horizontal m-t-30">
                            <input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo $id_cliente; ?>">

                            <div class="form-group">
                                <label for="estado" class="col-sm-12">Estado</label>
                                <div class="col-sm-12">
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="1" <?php echo ($estado == '1') ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo ($estado == '0') ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="nombre" class="col-sm-12">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre del cliente" value="<?php echo $nombre; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="apellido_paterno" class="col-sm-12">Apellido Paterno</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Ingrese el apellido paterno del cliente" value="<?php echo $apellido_paterno; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="apellido_materno" class="col-sm-12">Apellido Materno</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Ingrese el apellido materno del cliente" value="<?php echo $apellido_materno; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tipo_documento" class="col-sm-12">Tipo de Documento</label>
                                <div class="col-sm-12">
                                    <select class="form-control" id="tipo_documento" name="tipo_documento" required onchange="mostrarCampo(this.value)">
                                        <option value="" disabled selected>Seleccione el tipo de documento</option>
                                        <option value="DNI" <?php echo $tipo_documento === 'DNI' ? 'selected' : ''; ?>>DNI</option>
                                        <option value="RUC" <?php echo $tipo_documento === 'RUC' ? 'selected' : ''; ?>>RUC</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label id="lblDniRuc" for="dni_ruc" class="col-sm-12"><?php echo $tipo_documento === 'DNI' ? 'DNI' : 'RUC'; ?></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="dni_ruc" name="dni_ruc" placeholder="<?php echo $tipo_documento === 'DNI' ? 'Ingrese el DNI del cliente' : 'Ingrese el RUC del cliente'; ?>" value="<?php echo $dni_ruc; ?>" required maxlength="<?php echo $tipo_documento === 'DNI' ? '8' : '11'; ?>" pattern="<?php echo $tipo_documento === 'DNI' ? '^[0-9]{8}$' : '^[0-9]{11}$'; ?>">
                                </div>
                            </div>
                            <script>
                                function mostrarCampo(valor) {
                                    const dniRucInput = document.getElementById('dni_ruc');
                                    const dniRucLabel = document.getElementById('lblDniRuc');

                                    if (valor === 'DNI') {
                                        dniRucInput.maxLength = "8";
                                        dniRucInput.placeholder = "Ingrese el DNI del cliente";
                                        dniRucInput.pattern = "^[0-9]{8}$";
                                        dniRucLabel.innerHTML = "DNI";
                                    } else if (valor === 'RUC') {
                                        dniRucInput.maxLength = "11";
                                        dniRucInput.placeholder = "Ingrese el RUC del cliente";
                                        dniRucInput.pattern = "^[0-9]{11}$";
                                        dniRucLabel.innerHTML = "RUC";
                                    }
                                }
                            </script>
                            <div class="form-group">
                                <label for="celular" class="col-sm-12">Celular</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="celular" name="celular" placeholder="Ingrese el celular del cliente" value="<?php echo $celular; ?>" r>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-12">Email</label>
                                <div class="col-sm-12">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese el email del cliente" value="<?php echo $email; ?>" r>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="direccion" class="col-sm-12">Dirección</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ingrese la dirección del cliente" value="<?php echo $direccion; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="referencia" class="col-sm-12">Referencia</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="referencia" name="referencia" placeholder="Ingrese la referencia del cliente" value="<?php echo $referencia; ?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-warning" style="background-color: #EC672B; border-color: #EC672B;">Actualizar</button>
                                </div>
                            </div>
                        </form>

                        <!-- Contenedor para mensajes -->
                        <div id="mensajeExito" style="display:none; color:green; font-weight:bold;"></div>

                        <!-- JavaScript -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            $(document).ready(function() {
                                $("#editarClienteForm").on("submit", function(e) {
                                    e.preventDefault(); // Evitar que se recargue la página

                                    // Obtener datos del formulario
                                    const datos = {
                                        id_cliente: $("#id_cliente").val(),
                                        nombre: $("#nombre").val(),
                                        apellido_paterno: $("#apellido_paterno").val(),
                                        apellido_materno: $("#apellido_materno").val(),
                                        tipo_documento: $("#tipo_documento").val(),
                                        dni_ruc: $("#dni_ruc").val(),
                                        celular: $("#celular").val(),
                                        email: $("#email").val(),
                                        direccion: $("#direccion").val(),
                                        referencia: $("#referencia").val(),
                                        estado: $("#estado").val(),
                                    };

                                    // Enviar datos usando AJAX
                                    $.ajax({
                                        url: "../app/controllers/clientes/editar_cliente.php", // Archivo PHP que procesa la solicitud
                                        method: "POST",
                                        contentType: "application/json",
                                        data: JSON.stringify(datos), // Convertir datos a JSON
                                        success: function(respuesta) {
                                            if (respuesta.success) {
                                                // Mostrar mensaje de éxito
                                                $("#mensajeExito").text(respuesta.message).show();

                                                // Actualizar el formulario
                                                $("#editarClienteForm").load("../clientes/editar_cliente.php?id=" + datos.id_cliente + " #editarClienteForm > *");
                                            } else {
                                                // Mostrar mensaje de error
                                                alert(respuesta.message);
                                            }
                                        },
                                        error: function() {
                                            alert("Error al guardar el cliente.");
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

    <?php include('../layout/parte2.php'); ?>