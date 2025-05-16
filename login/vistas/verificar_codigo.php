


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
                <p class="login-box-msg text-danger">Ingrese su código de verificación para el usuario</p>
                <form action="../controllers/verificar_codigo.php?token=<?php echo $_GET['token']; ?>" method="POST">
                <!-- <form id="form-ingreso"> -->
                    <div class="input-group mb-3">
                        <input type="number" name="codigo" class="form-control" placeholder="Código de verificación" maxlength="4" minlength="4">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-key"></span>
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





<form action="../app/controllers/usuarios/verificar_codigo.php?token=<?php echo $_GET['token']; ?>" method="POST">