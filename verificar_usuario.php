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
        if (isset($_POST['nick'])) {
            $nick = $_POST['nick'];

            $sql = "SELECT * FROM `users` WHERE nick='" . $nick . "';";
            $resultado = $conn->query($sql);

            if ($resultado->num_rows > 0) {
                echo json_encode([
                    "codigo" => 200,
                    "mensaje" => "El usuario existe"
                ]);
            } else {
                echo json_encode([
                    "codigo" => 404,
                    "mensaje" => "El usuario NO existe"
                ]);
            }
        } else {
            echo json_encode([
                "codigo" => 400,
                "mensaje" => "Faltan datos para verificar el usuario"
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
