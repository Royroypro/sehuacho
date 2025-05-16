<?php
include_once('../../config.php');

// Indicamos que la respuesta será JSON
header('Content-Type: application/json');

// Inicializamos la respuesta
$response = [
    'success'     => false,
    'message'     => '',
    'id_cliente'  => null
];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    $response['message'] = "Método no permitido";
    echo json_encode($response);
    exit;
}

try {
    // Forzar modo excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recogemos y normalizamos los datos
    $id_cliente       = trim($_POST["id_cliente"]       ?? '');
    $nombre           = trim($_POST["nombre"]           ?? '');
    $apellido_paterno = trim($_POST["apellido_paterno"] ?? '');
    $apellido_materno = trim($_POST["apellido_materno"] ?? '');
    $tipo_documento   = trim($_POST["tipo_documento"]   ?? '');
    $dni_ruc          = trim($_POST["dni_ruc"]          ?? '');
    $celular          = trim($_POST["celular"]          ?? '');
    $emailRaw         = trim($_POST["email"]            ?? '');
    $direccion        = trim($_POST["direccion"]        ?? '');
    $referencia       = trim($_POST["referencia"]       ?? '');

    // Sólo el nombre es obligatorio
    if ($nombre === '') {
        $response['message'] = "Debe completar el campo obligatorio: Nombre";
        echo json_encode($response);
        exit;
    }

    // Normalizar opcionales: convertir '' a null
    $tipo_documento = ($tipo_documento === '') ? null : $tipo_documento;
    $dni_ruc        = ($dni_ruc === '')        ? null : $dni_ruc;
    $celular        = ($celular === '')        ? null : $celular;
    $email          = ($emailRaw === '')       ? null : $emailRaw;
    $direccion      = ($direccion === '')      ? null : $direccion;
    $referencia     = ($referencia === '')     ? null : $referencia;

    // Función para comprobar duplicados (excluye el propio ID en edición)
    function existsInClientes(PDO $pdo, string $field, $value, $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM clientes WHERE {$field} = :val";
        if ($excludeId !== null && is_numeric($excludeId)) {
            $sql .= " AND id_cliente != :id_exclude";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':val', $value);
        if (isset($excludeId)) {
            $stmt->bindValue(':id_exclude', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    // Verificar duplicados sólo si el campo no es null
    if ($dni_ruc !== null && existsInClientes($pdo, 'dni_ruc', $dni_ruc, $id_cliente)) {
        $response['message'] = "El DNI/RUC ya existe";
        echo json_encode($response);
        exit;
    }
    if ($email !== null && existsInClientes($pdo, 'email', $email, $id_cliente)) {
        $response['message'] = "El correo electrónico ya existe";
        echo json_encode($response);
        exit;
    }

    // Decidir INSERT o UPDATE según id_cliente
    if ($id_cliente !== '' && is_numeric($id_cliente)) {
        // —— FLUJO DE EDICIÓN ——
        $sql = "UPDATE clientes SET
                    nombre           = :nombre,
                    apellido_paterno = :apellido_paterno,
                    apellido_materno = :apellido_materno,
                    tipo_documento   = :tipo_documento,
                    dni_ruc          = :dni_ruc,
                    celular          = :celular,
                    email            = :email,
                    direccion        = :direccion,
                    referencia       = :referencia
                WHERE id_cliente = :id_cliente";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);
    } else {
        // —— FLUJO DE INSERCIÓN ——
        $sql = "INSERT INTO clientes (
                    nombre, apellido_paterno, apellido_materno,
                    tipo_documento, dni_ruc, celular,
                    email, direccion, referencia
                ) VALUES (
                    :nombre, :apellido_paterno, :apellido_materno,
                    :tipo_documento, :dni_ruc, :celular,
                    :email, :direccion, :referencia
                )";
        $stmt = $pdo->prepare($sql);
    }

    // Bind de todos los parámetros
    $stmt->bindValue(':nombre',           $nombre, PDO::PARAM_STR);
    $stmt->bindValue(':apellido_paterno', $apellido_paterno, PDO::PARAM_STR);
    $stmt->bindValue(':apellido_materno', $apellido_materno, PDO::PARAM_STR);

    // Campos opcionales con tratamiento de null
    $stmt->bindValue(':tipo_documento',
        $tipo_documento,
        $tipo_documento === null ? PDO::PARAM_NULL : PDO::PARAM_STR
    );
    $stmt->bindValue(':dni_ruc',
        $dni_ruc,
        $dni_ruc === null ? PDO::PARAM_NULL : PDO::PARAM_STR
    );
    $stmt->bindValue(':celular',
        $celular,
        $celular === null ? PDO::PARAM_NULL : PDO::PARAM_STR
    );
    $stmt->bindValue(':email',
        $email,
        $email === null ? PDO::PARAM_NULL : PDO::PARAM_STR
    );
    $stmt->bindValue(':direccion',
        $direccion,
        $direccion === null ? PDO::PARAM_NULL : PDO::PARAM_STR
    );
    $stmt->bindValue(':referencia',
        $referencia,
        $referencia === null ? PDO::PARAM_NULL : PDO::PARAM_STR
    );

    // Ejecutar consulta
    $stmt->execute();

    // Preparar respuesta
    if (empty($id_cliente)) {
        $response['id_cliente'] = $pdo->lastInsertId();
        $response['message']    = "Cliente creado correctamente";
    } else {
        $response['id_cliente'] = $id_cliente;
        $response['message']    = "Cliente actualizado correctamente";
    }
    $response['success'] = true;

} catch (PDOException $e) {
    http_response_code(500);
    $response['message'] = "Error en la base de datos: " . $e->getMessage();
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = "Error inesperado: " . $e->getMessage();
}

echo json_encode($response);
exit;
