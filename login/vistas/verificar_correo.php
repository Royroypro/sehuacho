<?php

include "../../app/config.php";

$id_usuario_sesion = $_GET['id_usuario_sesion'];

$stmt = $pdo->prepare("SELECT Nombre FROM usuario WHERE id = :id_usuario_sesion");
$stmt->bindParam('id_usuario_sesion', $id_usuario_sesion);
$stmt->execute();
$usuario = $stmt->fetch()['Nombre'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de ventas NINO</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../public/templeates/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../../public/templeates/AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../public/templeates/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <style>
        body {
            background-image: url("../portada.jpg");
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
    <!-- Libreria Sweetallert2-->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="hold-transition login-page">
    <div class="login-box" style="margin: 0 auto; width: 400px;">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary" style="margin: 0 auto; width: 400px;">
            <div class="card-header text-center">
                <a href="" class="h1"><b>Sistema de </b>VENTAS</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg text-danger">Su correo no está verificado para el usuario <?php echo $usuario ; ?> Ingrese un correo valido para  verificar</p>
                <form method="POST"action="../controllers/enviar_correo.php">
                <!-- <form id="form-ingreso"> -->
                    <input type="hidden" name="id_usuario_sesion" value="<?php echo $id_usuario_sesion; ?>">
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Enviar</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <div id="error-message" class="text-danger mt-2" style="display: none;"></div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
    <script>
        $(document).ready(function() {
            $('#form-ingreso').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '../controllers/ingreso.php',
                    type: 'POST',
                    data: $('#form-ingreso').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#error-message').hide();
                            window.location.href = '../home.php';
                        } else {
                            $('#error-message').text(response.message).show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $('#error-message').text("Ocurrio un error inesperado").show();
                    }
                });
            });
        });
    </script>
</body>
</html>

