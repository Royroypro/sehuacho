<?php
include_once '../app/config.php';
?>

<div id="main-wrapper">
    <?php include_once '../layout/parte1.php'; ?>
    <div class="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Crear Cliente</h4>
                        <h6 class="card-subtitle">Complete la información</h6>
                        <form id="crearClienteForm" class="form-horizontal m-t-30">
                            <div class="form-group">
                                <label for="nombre" class="col-sm-12">Nombre <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre del cliente" required aria-label="Nombre del cliente">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="apellido_paterno" class="col-sm-12">Apellido Paterno</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Ingrese el apellido paterno del cliente" aria-label="Apellido paterno">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="apellido_materno" class="col-sm-12">Apellido Materno</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Ingrese el apellido materno del cliente" aria-label="Apellido materno">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tipo_documento" class="col-sm-12">Tipo de documento <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <select class="form-control" id="tipo_documento" name="tipo_documento" onchange="mostrarCampo(this.value)" aria-label="Tipo de documento">
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label id="lblDniRuc" for="dni_ruc" class="col-sm-12">DNI <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="dni_ruc" name="dni_ruc" placeholder="Ingrese el DNI del cliente" maxlength="8" pattern="^[0-9]{8}$" aria-label="Número de documento">
                                    <small id="dniRucError" class="text-danger" style="display:none;">Por favor, ingrese un DNI/RUC válido.</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="celular" class="col-sm-12">Celular</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="celular" name="celular" placeholder="Ingrese el celular del cliente" aria-label="Número de celular">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-12">Email</label>
                                <div class="col-sm-12">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese el email del cliente" aria-label="Correo electrónico">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="direccion" class="col-sm-12">Dirección</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ingrese la dirección del cliente" aria-label="Dirección del cliente">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="referencia" class="col-sm-12">Referencia</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="referencia" name="referencia" placeholder="Ingrese la referencia del cliente" aria-label="Referencia del cliente">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-warning" style="background-color: #EC672B; border-color: #EC672B;">Crear</button>
                                </div>
                            </div>
                            <div id="mensajeExito" style="display:none; color:green; font-weight:bold;">Guardado correctamente.</div>
                        </form>

                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            function mostrarCampo(valor) {
                                const dniRucInput = document.getElementById('dni_ruc');
                                const dniRucLabel = document.getElementById('lblDniRuc');
                                const dniRucError = document.getElementById('dniRucError');

                                dniRucError.style.display = 'none'; // Ocultar mensaje de error

                                if (valor === 'DNI') {
                                    dniRucInput.maxLength = 8;
                                    dniRucInput.placeholder = "Ingrese el DNI del cliente (opcional)";
                                    dniRucInput.pattern = "^[0-9]{8}$";
                                    dniRucLabel.textContent = "DNI";
                                } else if (valor === 'RUC') {
                                    dniRucInput.maxLength = 11;
                                    dniRucInput.placeholder = "Ingrese el RUC del cliente (opcional)";
                                    dniRucInput.pattern = "^[0-9]{11}$";
                                    dniRucLabel.textContent = "RUC";
                                }
                            }

                            $(document).ready(function() {
                                $("#crearClienteForm").on("submit", function(e) {
                                    e.preventDefault();

                                    // Validación cliente-side: solo nombre es obligatorio
                                    const nombreVal = $("#nombre").val().trim();
                                    if (nombreVal === "") {
                                        alert("Debe completar el campo obligatorio: Nombre");
                                        return;
                                    }

                                    // Construimos datos sólo con campos no vacíos
                                    const datos = {
                                        nombre: nombreVal
                                    };
                                    [
                                        'apellido_paterno',
                                        'apellido_materno',
                                        'tipo_documento',
                                        'dni_ruc',
                                        'celular',
                                        'email',
                                        'direccion',
                                        'referencia'
                                    ].forEach(field => {
                                        const val = $("#" + field).val().trim();
                                        if (val !== "") {
                                            datos[field] = val;
                                        }
                                    });

                                    $.ajax({
                                        url: "../app/controllers/clientes/crear_cliente.php",
                                        method: "POST",
                                        data: datos,
                                        dataType: "json",
                                        success: function(respuesta) {
                                            if (respuesta.success) {
                                                $("#mensajeExito").text(respuesta.message).show();
                                                $("#crearClienteForm")[0].reset();
                                                $("#lblDniRuc").text("DNI/RUC");
                                                $("#dniRucError").hide();
                                            } else if (respuesta.message === "El DNI/RUC ya existe") {
                                                alert("El DNI/RUC ya existe.");
                                                $("#mensajeExito").hide();
                                            } else if (respuesta.message === "El correo electrónico ya existe") {
                                                alert("El correo electrónico ya existe.");
                                                $("#mensajeExito").hide();
                                            } else {
                                                alert(respuesta.message);
                                                $("#mensajeExito").hide();
                                            }
                                        },
                                        error: function() {
                                            alert("Error al guardar el cliente. Intente nuevamente.");
                                            $("#mensajeExito").hide();
                                        }
                                    });
                                });

                                // Validación de patrón sólo si hay valor
                                $("#dni_ruc").on("input", function() {
                                    const input = $(this);
                                    const val = input.val().trim();
                                    const pattern = new RegExp(input.attr("pattern"));
                                    const dniRucError = $("#dniRucError");

                                    if (val !== "" && !pattern.test(val)) {
                                        dniRucError.show();
                                    } else {
                                        dniRucError.hide();
                                    }
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