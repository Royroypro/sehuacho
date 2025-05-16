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
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="login-container">
        <h2>Recuperar contraseña</h2>

        <!-- Formulario de login -->
        <form  action="../controllers/recuperar_contraseña.php" method="POST">
            <div class="input-container">
                <label class="form-label" for="email">Correo electrónico:</label>
                <input class="form-control" type="email" id="email" name="email" required style="width: 100%; border-radius: 10px; height: 30px;">
            </div>
            <!-- Mensaje de error -->
            <div id="error-message" style="color: green; display: none;">&nbsp;</div>
            <script>
                setTimeout(function () {
                    $('#error-message').hide();
                }, 20000);
            </script>

            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
        <p class="olvidar-contraseña"><a href="index.php">Volver a Iniciar sesión</a></p>
    </div>
</div>

    <script>
        // Manejo del formulario con AJAX
        $('#recuperarForm').on('submit', function(e) {
            e.preventDefault(); // Evitar el envío normal del formulario

            // Obtener los datos del formulario
            var formData = {
                'email': $('#email').val()
            };

            // Enviar los datos del formulario a login.php con AJAX
            $.ajax({
                type: 'POST',
                url: '../controllers/recuperar_contraseña.php', // Cambia esto si tu archivo PHP está en otra ubicación

                data: formData,
                success: function(response) {
                    // Si la respuesta es "success", mostrar mensaje de éxito
                    if (response === 'success') {
                        $('#error-message').text('Se ha enviado un correo electrónico con instrucciones para recuperar tu contraseña.').show();
                    } else {
                        // Si es un mensaje de error, mostrarlo
                        $('#error-message').text(response).show();
                    }
                },
                error: function() {
                    $('#error-message').text('Hubo un error al procesar la solicitud.').show();
                }
            });
        });
    </script>
</body>
</html>

