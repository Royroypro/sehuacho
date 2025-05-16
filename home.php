<?php
    include_once 'app/config.php';
    include_once 'layout/sesion.php';
  
?>

<div id="main-wrapper">
<?php
    
    include_once 'layout/parte1.php';
?>

    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-12 d-flex no-block align-items-center">
                    <h4 class="page-title">Dashboard</h4>
                    <div class="ml-auto text-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Library</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="container-fluid">
            <!-- ============================================================== -->
            <!-- Sales Cards  -->
            <!-- ============================================================== -->
            <div class="row">
                <!-- Column -->
                <!-- <div class="col-md-6 col-lg-2 col-xlg-3">
                    <div class="card card-hover">
                        <a href="venta_planes/lista_venta.php">
                            <div class="box bg-cyan text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-currency-usd"></i></h1>
                                <?php
                                   /*  $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM cliente_planes");
                                    $stmt->execute();
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC); */
                                ?>
                                <h6 class="text-white">Venta Planes (<?php /* echo $row['total']; */ ?>)</h6>
                            </div>
                        </a>
                    </div>
                </div> -->
                <!-- Column -->
                <!-- <div class="col-md-6 col-lg-4 col-xlg-3">
                    <div class="card card-hover">
                        <a href="facturas/lista_recibos.php">
                            <div class="box bg-success text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-receipt"></i></h1>
                                <?php
                                   /*  $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM recibos");
                                    $stmt->execute();
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC); */
                                ?>
                                <h6 class="text-white">Recibos (<?php /* echo $row['total']; */ ?>)</h6>
                            </div>
                        </a>
                    </div>
                </div> -->
                <!-- Column -->
               <!--  <div class="col-md-6 col-lg-2 col-xlg-3">
                    <div class="card card-hover">
                        <div class="box bg-warning text-center">
                            <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                            <h6 class="text-white">Widgets</h6>
                        </div>
                    </div>
                </div> -->
                <!-- Column -->
                <!-- <div class="col-md-6 col-lg-2 col-xlg-3">
                    <div class="card card-hover">
                        <div class="box bg-danger text-center">
                            <h1 class="font-light text-white"><i class="mdi mdi-border-outside"></i></h1>
                            <h6 class="text-white">Tables</h6>
                        </div>
                    </div>
                </div> -->
                <!-- Column -->
                <!-- <div class="col-md-6 col-lg-2 col-xlg-3">
                    <div class="card card-hover">
                        <div class="box bg-info text-center">
                            <h1 class="font-light text-white"><i class="mdi mdi-arrow-all"></i></h1>
                            <h6 class="text-white">Full Width</h6>
                        </div>
                    </div>
                </div> -->
                <!-- Column -->
                <!-- Column -->
                <!-- <div class="col-md-6 col-lg-4 col-xlg-3">
                    <div class="card card-hover">
                        <div class="box bg-danger text-center">
                            <h1 class="font-light text-white"><i class="mdi mdi-receipt"></i></h1>
                            <h6 class="text-white">Forms</h6>
                        </div>
                    </div>
                </div> -->
                <!-- Column -->
                <!-- <div class="col-md-6 col-lg-2 col-xlg-3">
                    <div class="card card-hover">
                        <div class="box bg-info text-center">
                            <h1 class="font-light text-white"><i class="mdi mdi-relative-scale"></i></h1>
                            <h6 class="text-white">Buttons</h6>
                        </div>
                    </div>
                </div> -->
                <!-- Column -->
                <!-- <div class="col-md-6 col-lg-2 col-xlg-3">
                    <div class="card card-hover">
                        <div class="box bg-cyan text-center">
                            <h1 class="font-light text-white"><i class="mdi mdi-pencil"></i></h1>
                            <h6 class="text-white">Elements</h6>
                        </div>
                    </div>
                </div> -->
                <!-- Column -->
                <!-- <div class="col-md-6 col-lg-2 col-xlg-3">
                    <div class="card card-hover">
                        <div class="box bg-success text-center">
                            <h1 class="font-light text-white"><i class="mdi mdi-calendar-check"></i></h1>
                            <h6 class="text-white">Calnedar</h6>
                        </div>
                    </div>
                </div> -->
                <!-- Column -->
                <!-- <div class="col-md-6 col-lg-2 col-xlg-3">
                    <div class="card card-hover">
                        <div class="box bg-warning text-center">
                            <h1 class="font-light text-white"><i class="mdi mdi-alert"></i></h1>
                            <h6 class="text-white">Errors</h6>
                        </div>
                    </div>
                </div> -->
                <!-- Column -->
            </div>
            <!-- ============================================================== -->
            <!-- Sales chart -->
            <!-- ============================================================== -->
            
            <!-- ============================================================== -->
            <!-- Sales chart -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Recent comment and chats -->
            <!-- ============================================================== -->

            <!-- ============================================================== -->
            <!-- Recent comment and chats -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->

    </div>
</div>

<?php include("layout/parte2.php"); ?>