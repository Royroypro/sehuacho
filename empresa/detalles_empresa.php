<?php
include_once '../app/config.php';
include_once '../layout/sesion.php';

$id = 1;
$stmt = $pdo->prepare("SELECT e.ruc, e.razon_social, e.nombre_comercial, d.ubigeo, d.departamento, d.provincia, d.distrito, d.urbanizacion, d.direccion, d.cod_local
                      FROM empresas e
                      LEFT JOIN direccion d ON e.id_direccion = d.id
                      WHERE e.id = :id");
$stmt->execute(['id' => $id]);
$empresa = $stmt->fetch();
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
                            <h4 class="card-title">Detalles de la empresa</h4>
                            <ul class="list-group">
                                <li class="list-group-item">Razón Social: <?php echo $empresa['razon_social']; ?></li>
                                <li class="list-group-item">RUC: <?php echo $empresa['ruc']; ?></li>
                                <li class="list-group-item">Dirección: <?php echo $empresa['direccion']; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include  "../layout/parte2.php"; ?>
