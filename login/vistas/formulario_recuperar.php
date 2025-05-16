<!DOCTYPE html>
<html lang="es">

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
    <script>
        $(document).ready(function() {
            $('#formulario_nueva_contraseña').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "../controllers/nueva_contraseña.php",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response == 'success') {
                            let countdown = 10;
                            const interval = setInterval(function() {
                                $('#error-message').html(`Contraseña actualizada. Será redirigido a la página de inicio de sesión en ${countdown} segundos.`);
                                countdown--;
                                if (countdown < 0) {
                                    clearInterval(interval);
                                    window.location.href = '../vistas';
                                }
                            }, 500);
                        } else {
                            $('#error-message').html(response);
                        }
                    }
                });
            });
        });
    </script>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="login-container">
            <h2 class="text-center">Establecer nueva contraseña</h2>

            <!-- Formulario para nueva contraseña -->
            <form id="formulario_nueva_contraseña" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="input-container mb-3">
                    <label class="form-label" for="nueva_contraseña">Nueva Contraseña:</label>
                    <input class="form-control" type="password" id="nueva_contraseña" name="nueva_contraseña" required style="width: 80%; border-radius: 10px;">
                </div>
                <div class="input-container mb-3">
                    <label class="form-label" for="repetir_contraseña">Repetir Contraseña:</label>
                    <input class="form-control" type="password" id="repetir_contraseña" name="repetir_contraseña" required style="width: 80%; border-radius: 10px;">
                </div>
                <!-- Mensaje de error -->
                <div id="error-message" class="text-success mb-3"><?php echo $_GET['message'] ?? ''; ?></div>
                <button type="submit" class="btn btn-primary w-100">Enviar</button>
            </form>
            <p class="text-center mt-3"><a href="index.php">Volver a Iniciar sesión</a></p>
        </div>
    </div>
</body>
</html>

