<?php
include_once '../app/config.php';
include_once '../app/controllers/clientes/consultar_cliente.php';
?>

<div id="main-wrapper">
    <?php include_once '../layout/parte1.php'; ?>
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
        }
        ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Detalle del Cliente</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                                <tr>
                                                    <td>Estado</td>
                                                    <td><?php echo $cliente['estado'] == '1' ? '<span style="color:green;">Activo</span>' : '<span style="color:red;">Inactivo</span>'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Nombre</td>
                                                    <td><?php echo $nombre; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Apellido Paterno</td>
                                                    <td><?php echo $apellido_paterno; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Apellido Materno</td>
                                                    <td><?php echo $apellido_materno; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>DNI/RUC</td>
                                                    <td><?php echo $dni_ruc; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Tipo de Documento</td>
                                                    <td><?php echo $tipo_documento; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Celular</td>
                                                    <td><?php echo $celular; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td><?php echo $email; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Dirección</td>
                                                    <td><?php echo $direccion; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Referencia</td>
                                                    <td><?php echo $referencia; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-left">
                    <a href="lista_cliente.php" class="btn btn-primary">Regresar</a>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/parte2.php'); ?>

