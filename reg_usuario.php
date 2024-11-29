<?php
include 'header.php';

try {
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
    if (!$conn) {
        echo json_encode([
            "codigo" => 500,
            "mensaje" => "Error intentando conectar"
        ]);
    } else {
        // Cambiar $_GET a $_POST
        if (isset($_POST['nick']) && isset($_POST['passwd'])) {
            $nick = $_POST['nick'];
            $passwd = $_POST['passwd'];

            // Verificar si el usuario ya existe
            $sql = "SELECT * FROM `users` WHERE nick='" . $nick . "';";
            $resultado = $conn->query($sql);

            if ($resultado->num_rows > 0) {
                echo json_encode([
                    "codigo" => 409, // Código 409: Conflicto (usuario ya existe)
                    "mensaje" => "Ya existe un usuario registrado con ese nombre"
                ]);
            } else {
                // Crear un nuevo usuario
                $sql = "INSERT INTO `users` (`id`, `nick`, `passwd`, `kills`, `deaths`, `games_played`) 
                        VALUES (NULL, '" . $nick . "', '" . $passwd . "', 0, 0, 0);";

                if ($conn->query($sql) === TRUE) {
                    echo json_encode([
                        "codigo" => 201, // Código 201: Creado
                        "mensaje" => "Usuario creado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "codigo" => 500,
                        "mensaje" => "Error intentando crear el usuario"
                    ]);
                }
            }
        } else {
            echo json_encode([
                "codigo" => 400, // Código 400: Solicitud incorrecta
                "mensaje" => "Faltan datos para crear el usuario"
            ]);
        }
    }
} catch (Exception $e) {
    echo json_encode([
        "codigo" => 500,
        "mensaje" => "Error intentando conectar"
    ]);
}

include 'fooder.php';
