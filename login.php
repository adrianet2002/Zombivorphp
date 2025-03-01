<?php
include 'header.php';

try {
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
    if (!$conn) {
        echo "Error intentando conectar";
    } else {
        // Cambiar $_GET a $_POST
        if (isset($_POST['nick']) && isset($_POST['passwd'])) {
            $nick = $_POST['nick'];
            $passwd = $_POST['passwd'];

            $sql = "SELECT id, nick FROM `users` WHERE nick='" . $nick . "' and passwd='" . $passwd . "';";
            $resultado = $conn->query($sql);

            if ($resultado->num_rows > 0) {
              
                $usuario = $resultado->fetch_assoc(); // Obtenemos el resultado como array asociativo
                session_start(); // Inicia la sesión en PHP
                $_SESSION['user_id'] = $usuario['id']; 
              
                echo json_encode([
                    "codigo" => 200,
                    "mensaje" => "Inicio de sesión correcto",
                    "id" => $usuario['id'], // Enviamos el ID como parte de la respuesta
                    "nick" => $usuario['nick']
                   
                ]);
            } else {
                echo json_encode([
                    "codigo" => 404,
                    "mensaje" => "Usuario o contraseña incorrecto"
                ]);
            }
        } else {
            echo json_encode([
                "codigo" => 400,
                "mensaje" => "Faltan datos para hacer el login"
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