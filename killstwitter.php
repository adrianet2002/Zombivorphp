<?php
include 'header.php';

try {
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
    if (!$conn) {
        echo json_encode([
            "codigo" => 500,
            "mensaje" => "Error al conectar con la base de datos"
        ]);
    } else {
        // Verificar si se envió el parámetro id_usuario
        if (isset($_POST['id_usuario'])) {
            $user_id = mysqli_real_escape_string($conn, $_POST['id_usuario']); // Escapa el parámetro para mayor seguridad

            // Consulta para obtener las kills del usuario
            $sql = "SELECT kills FROM `users` WHERE id='" . $user_id . "';";
            $resultado = $conn->query($sql);

            if ($resultado->num_rows > 0) {
                $usuario = $resultado->fetch_assoc(); // Obtenemos las kills como array asociativo

                echo json_encode([
                    "codigo" => 200,
                    "mensaje" => "Kills obtenidas correctamente",
                    "kills" => intval($usuario['kills']) // Asegúrate de que el valor sea un número entero
                ]);
            } else {
                echo json_encode([
                    "codigo" => 404,
                    "mensaje" => "Usuario no encontrado"
                ]);
            }
        } else {
            echo json_encode([
                "codigo" => 400,
                "mensaje" => "Falta el parámetro id_usuario"
            ]);
        }
    }
} catch (Exception $e) {
    echo json_encode([
        "codigo" => 500,
        "mensaje" => "Error en el servidor: " . $e->getMessage()
    ]);
}

include 'fooder.php';
