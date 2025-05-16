<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="plan/image/png" sizes="16x16" href="assets/images/favicon.png">
    <title>Matrix Template - The Ultimate Multipurpose admin template</title>
    <link href="<?php echo $URL; ?>/plan/assets/libs/flot/css/float-chart.css" rel="stylesheet">
    <link href="<?php echo $URL; ?>/plan/dist/css/style.min.css" rel="stylesheet">
    </head>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>


    <header class="topbar" data-navbarbg="skin5">
        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <div class="navbar-header" data-logobg="skin5">
                <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                <a class="navbar-brand" href="<?php echo $URL; ?>/empresa/detalles_empresa.php">
                    <b class="logo-icon p-l-10">
                        <img src="<?php echo $URL; ?>/plan/assets/images/logo.png" alt="homepage" class="light-logo" />

                    </b>
                    <span class="logo-text">
                        </span>

                </a>
                <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
            </div>
            <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                <ul class="navbar-nav float-left mr-auto">
                    <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>

                </ul>
                <ul class="navbar-nav float-right">

                    <li class="nav-item dropdown">

                        <a style="margin-top: 10px; margin-bottom: -20px;" class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $URL; ?>/plan/assets/images/users/1.jpg" alt="user" class="rounded-circle" width="31"></a>
                        <div class="dropdown-menu dropdown-menu-right user-dd animated">
                            <a class="dropdown-item" href="<?php echo $URL; ?>usuarios/detalles_usuario.php"><i class="ti-user m-r-5 m-l-5"></i> Mi Perfil de Usuario</a>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo $URL; ?>login/controllers/cerrar_sesion.php"><i class="fa fa-power-off m-r-5 m-l-5"></i> Cerrar Sesión</a>
                            <div class="dropdown-divider"></div>
                           </div>
                    </li>
                    </ul>
            </div>
        </nav>
    </header>

    <aside class="left-sidebar" data-sidebarbg="skin5">
        <div class="scroll-sidebar">
            <nav class="sidebar-nav">
                <ul id="sidebarnav" class="p-t-30">
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="<?php echo $URL; ?>home.php" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a></li>

                    <li class="sidebar-item active" id="menu-productos">
                        <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="true">
                            <i class="mdi mdi-package-variant"></i><span class="hide-menu">Productos </span>
                        </a>
                        <ul aria-expanded="true" class="collapse first-level" id="submenu-productos" style="margin-left: 5mm; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                            <li class="sidebar-item"><a href="<?php echo $URL; ?>productos/crear_producto.php" class="sidebar-link"><i class="mdi mdi-package"></i><span class="hide-menu"> Registrar Productos </span></a></li>
                            <li class="sidebar-item"><a href="<?php echo $URL; ?>productos/lista_productos.php" class="sidebar-link"><i class="mdi mdi-package-variant-closed"></i><span class="hide-menu"> Lista de Productos </span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item active" id="menu-servicios">
                        <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="true">
                            <i class="mdi mdi-settings"></i><span class="hide-menu">Servicios </span>
                        </a>
                        <ul aria-expanded="true" class="collapse first-level" id="submenu-servicios" style="margin-left: 5mm; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                            <li class="sidebar-item"><a href="<?php echo $URL; ?>servicios/crear_servicio.php" class="sidebar-link"><i class="mdi mdi-settings-box"></i><span class="hide-menu"> Registrar Servicios </span></a></li>
                            <li class="sidebar-item"><a href="<?php echo $URL; ?>servicios/lista_servicios.php" class="sidebar-link"><i class="mdi mdi-settings"></i><span class="hide-menu"> Lista de Servicios </span></a></li>
                        </ul>
                    </li>


                    <li class="sidebar-item active" id="menu-clientes">
                        <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="true">
                            <i class="mdi mdi-account-multiple"></i><span class="hide-menu">Clientes </span>
                        </a>
                        <ul aria-expanded="true" class="collapse first-level" id="submenu-clientes" style="margin-left: 5mm; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                            <li class="sidebar-item"><a href="<?php echo $URL; ?>clientes/crear_cliente.php" class="sidebar-link"><i class="mdi mdi-account-outline"></i><span class="hide-menu"> Registrar Clientes </span></a></li>
                            <li class="sidebar-item"><a href="<?php echo $URL; ?>clientes/lista_cliente.php" class="sidebar-link"><i class="mdi mdi-account-plus"></i><span class="hide-menu"> Lista de Clientes </span></a></li>
                        </ul>
                    </li>


                    
                    <li class="sidebar-item active" id="menu-cotizaciones">
                        <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="true">
                            <i class="mdi mdi-file-document"></i><span class="hide-menu">Cotizaciones </span>
                        </a>
                        <ul aria-expanded="true" class="collapse first-level" id="submenu-cotizaciones" style="margin-left: 5mm; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                            <li class="sidebar-item"><a href="<?php echo $URL; ?>cotizaciones/crear_cotizacion.php" class="sidebar-link"><i class="mdi mdi-file-document"></i><span class="hide-menu"> Crear Cotización</span></a></li>
                            <li class="sidebar-item"><a href="<?php echo $URL; ?>cotizaciones/lista_cotizaciones.php" class="sidebar-link"><i class="mdi mdi-note-plus"></i><span class="hide-menu"> Lista de Cotizaciones </span></a></li>
                            

                        </ul>
                    </li>

                    <li class="sidebar-item active" id="menu-facturas">
                        <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="true">
                            <i class="mdi mdi-receipt"></i><span class="hide-menu">Facturas </span>
                        </a>
                        <ul aria-expanded="true" class="collapse first-level" id="submenu-facturas" style="margin-left: 5mm; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                            
                            <li class="sidebar-item"><a href="<?php echo $URL;?>/facturas/emitir_recibo.php" class="sidebar-link"><i class="mdi mdi-receipt"></i><span class="hide-menu">Emitir Recibo </span></a></li>
                            
                            <li class="sidebar-item"><a href="<?php echo $URL;?>/facturas/lista_recibos.php" class="sidebar-link"><i class="mdi mdi-note-plus"></i><span class="hide-menu"> lista de recibos</span></a></li>
                        </ul>
                    </li>


                </ul>
            </nav>
            </div>
        </aside>
    
</body>

</html>

<script>
    //script para el menu lateral


    document.addEventListener("DOMContentLoaded", function() {
        const menuItem = document.getElementById("menu-clientes");
        const submenu = document.getElementById("submenu-clientes");

        // Restaurar el estado del menú desde localStorage
        const isExpanded = localStorage.getItem("menu-clientes-expanded") === "true";

        if (isExpanded) {
            menuItem.classList.add("active");
            submenu.style.display = "block"; // Mostrar el submenú
        } else {
            menuItem.classList.remove("active");
            submenu.style.display = "none"; // Ocultar el submenú
        }

        // Manejar clics en el enlace principal del menú
        menuItem.querySelector("a").addEventListener("click", function() {
            const currentlyExpanded = menuItem.classList.contains("active");

            if (currentlyExpanded) {
                menuItem.classList.remove("active");
                submenu.style.display = "none"; // Ocultar
                localStorage.setItem("menu-clientes-expanded", "false");
            } else {
                menuItem.classList.add("active");
                submenu.style.display = "block"; // Mostrar
                localStorage.setItem("menu-clientes-expanded", "true");
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const menuItem = document.getElementById("menu-facturas");
        const submenu = document.getElementById("submenu-facturas");

        // Restaurar el estado del menú desde localStorage
        const isExpanded = localStorage.getItem("menu-facturas-expanded") === "true";

        if (isExpanded) {
            menuItem.classList.add("active");
            submenu.style.display = "block"; // Mostrar el submenú
        } else {
            menuItem.classList.remove("active");
            submenu.style.display = "none"; // Ocultar el submenú
        }

        // Manejar clics en el enlace principal del menú
        menuItem.querySelector("a").addEventListener("click", function() {
            const currentlyExpanded = menuItem.classList.contains("active");

            if (currentlyExpanded) {
                menuItem.classList.remove("active");
                submenu.style.display = "none"; // Ocultar
                localStorage.setItem("menu-facturas-expanded", "false");
            } else {
                menuItem.classList.add("active");
                submenu.style.display = "block"; // Mostrar
                localStorage.setItem("menu-facturas-expanded", "true");
            }
        });
    });


    document.addEventListener("DOMContentLoaded", function() {
        const menuItem = document.getElementById("menu-productos");
        const submenu = document.getElementById("submenu-productos");

        // Restaurar el estado del menú desde localStorage
        const isExpanded = localStorage.getItem("menu-productos-expanded") === "true";

        if (isExpanded) {
            menuItem.classList.add("active");
            submenu.style.display = "block"; // Mostrar el submenú
            submenu.style.marginLeft = "5mm";
            submenu.style.boxShadow = "0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19)";
        } else {
            menuItem.classList.remove("active");
            submenu.style.display = "none"; // Ocultar el submenú
        }

        // Manejar clics en el enlace principal del menú
        menuItem.querySelector("a").addEventListener("click", function() {
            const currentlyExpanded = menuItem.classList.contains("active");

            if (currentlyExpanded) {
                menuItem.classList.remove("active");
                submenu.style.display = "none"; // Ocultar
                localStorage.setItem("menu-productos-expanded", "false");
            } else {
                menuItem.classList.add("active");
                submenu.style.display = "block"; // Mostrar
                localStorage.setItem("menu-productos-expanded", "true");
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const menuItem = document.getElementById("menu-cotizaciones");
        const submenu = document.getElementById("submenu-cotizaciones");

        // Restaurar el estado del menú desde localStorage
        const isExpanded = localStorage.getItem("menu-cotizaciones-expanded") === "true";

        if (isExpanded) {
            menuItem.classList.add("active");
            submenu.style.display = "block"; // Mostrar el submenú
            submenu.style.marginLeft = "5mm";
            submenu.style.boxShadow = "0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19)";
        } else {
            menuItem.classList.remove("active");
            submenu.style.display = "none"; // Ocultar el submenú
        }

        // Manejar clics en el enlace principal del menú
        menuItem.querySelector("a").addEventListener("click", function() {
            const currentlyExpanded = menuItem.classList.contains("active");

            if (currentlyExpanded) {
                menuItem.classList.remove("active");
                submenu.style.display = "none"; // Ocultar
                localStorage.setItem("menu-cotizaciones-expanded", "false");
            } else {
                menuItem.classList.add("active");
                submenu.style.display = "block"; // Mostrar
                localStorage.setItem("menu-cotizaciones-expanded", "true");
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const menuItem = document.getElementById("menu-servicios");
        const submenu = document.getElementById("submenu-servicios");

        // Restaurar el estado del menú desde localStorage
        const isExpanded = localStorage.getItem("menu-servicios-expanded") === "true";

        if (isExpanded) {
            menuItem.classList.add("active");
            submenu.style.display = "block"; // Mostrar el submenú
            submenu.style.marginLeft = "5mm";
            submenu.style.boxShadow = "0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19)";
        } else {
            menuItem.classList.remove("active");
            submenu.style.display = "none"; // Ocultar el submenú
        }

        // Manejar clics en el enlace principal del menú
        menuItem.querySelector("a").addEventListener("click", function() {
            const currentlyExpanded = menuItem.classList.contains("active");

            if (currentlyExpanded) {
                menuItem.classList.remove("active");
                submenu.style.display = "none"; // Ocultar
                localStorage.setItem("menu-servicios-expanded", "false");
            } else {
                menuItem.classList.add("active");
                submenu.style.display = "block"; // Mostrar
                localStorage.setItem("menu-servicios-expanded", "true");
            }
        });
    });
</script>
