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
                            <h4 class="card-title">Detalles del usuario con ID: <?php echo $id_usuario_sesion; ?></h4>
                            <ul class="list-group">
                                <li class="list-group-item">Nombre: <?php echo $nombres_sesion; ?></li>
                                <li class="list-group-item">Correo: <?php echo $_SESSION['sesion_email']; ?></li>
                                <li class="list-group-item">Rol: <?php echo $rol_sesion; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Detalles del empleado con ID: <?php echo $id_empleado_sesion; ?></h4>
                            <ul class="list-group">
                                <li class="list-group-item">Nombre: <?php echo $nombres_empleado; ?></li>
                                <li class="list-group-item">Apellido Paterno: <?php echo $apellido_paterno_empleado; ?></li>
                                <li class="list-group-item">Apellido Materno: <?php echo $apellido_materno_empleado; ?></li>
                                <li class="list-group-item">DNI: <?php echo $dni_empleado; ?></li>
                                <li class="list-group-item">Fecha de nacimiento: <?php echo $fecha_nacimiento_empleado; ?></li>
                                <li class="list-group-item">Sexo: <?php echo $sexo_empleado; ?></li>
                                <li class="list-group-item">Sueldo: <?php echo $sueldo_empleado; ?></li>
                                <li class="list-group-item">Correo: <?php echo $correo_empleado; ?></li>
                                <li class="list-group-item">Celular: <?php echo $celular_empleado; ?></li>
                                <li class="list-group-item">Direccion: <?php echo $direccion_empleado; ?></li>
                                <li class="list-group-item">Cargo: <?php echo $cargo_empleado; ?></li>
                                <li class="list-group-item">Estado: <?php echo $estado_empleado; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include  "../layout/parte2.php";?>