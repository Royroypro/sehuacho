<?php

session_start();

if(isset($_SESSION['sesion_email'])){
    
    $email_sesion = $_SESSION['sesion_email'];
    $sql = "SELECT u.id, u.Nombre, u.Correo, tu.Nombre as rol, u.id_Empleado
                  FROM usuario as u INNER JOIN tipo_usuario as tu ON u.id_Tipo_Usuario = tu.id WHERE correo=:email_sesion";
    $query = $pdo->prepare($sql);
    $query->execute(['email_sesion'=>$email_sesion]);
    $usuarios = $query->fetch(PDO::FETCH_ASSOC);
    
    $id_usuario_sesion = $usuarios['id'];
    $nombres_sesion = $usuarios['Nombre'];
    $rol_sesion = $usuarios['rol'];
    $id_empleado_sesion = $usuarios['id_Empleado'];


    $sql2 = "SELECT e.id, e.Nombre, e.Apellido_Paterno, e.Apellido_Materno, e.DNI, e.Fecha_de_Nacimiento, e.Sexo, e.Sueldo, e.Correo, e.Celular, e.Direccion, c.Nombre_Cargo as cargo, e.Estado 
    FROM empleado as e INNER JOIN cargo as c ON e.id_Cargo = c.id WHERE Correo=:email_sesion";
    $query2 = $pdo->prepare($sql2);
    $query2->execute(['email_sesion'=>$email_sesion]);
    $empleados = $query2->fetchAll(PDO::FETCH_ASSOC);
    foreach ($empleados as $empleado){
        $id_empleado_sesion = $empleado['id'];
        $nombres_empleado = $empleado['Nombre'];
        $apellido_paterno_empleado = $empleado['Apellido_Paterno'];
        $apellido_materno_empleado = $empleado['Apellido_Materno'];
        $dni_empleado = $empleado['DNI'];
        $fecha_nacimiento_empleado = $empleado['Fecha_de_Nacimiento'];
        $sexo_empleado = $empleado['Sexo'];
        $sueldo_empleado = $empleado['Sueldo'];
        $correo_empleado = $empleado['Correo'];
        $celular_empleado = $empleado['Celular'];
        $direccion_empleado = $empleado['Direccion'];
        $cargo_empleado = $empleado['cargo'];
        $estado_empleado = $empleado['Estado'];
    }
}else{
    /* echo "no existe sesion"; */
    header('Location: '.$URL.'/index.php');
}


