<?php
include 'header.php'; 
session_start();

try {
    // Conexión a la base de datos
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
    if (!$conn) {
        echo json_encode([
            "codigo" => 500,
            "mensaje" => "Error al conectar con la base de datos"
        ]);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            "codigo" => 401, // No autorizado
            "mensaje" => "El jugador no está logueado"
        ]);
        exit;
    }

    $id_user = $_SESSION['user_id']; // ID del usuario logueado

    // Verificar que se envíen los datos necesarios
    if (isset($_POST['kills']) && isset($_POST['deaths'])) {
        $kills = intval($_POST['kills']);
        $deaths = intval($_POST['deaths']);

        // Manejar los jugadores adicionales dinámicamente
       
        $id_user2 = isset($_POST['id_user2']) ? intval($_POST['id_user2']) : null;
        $id_user3 = isset($_POST['id_user3']) ? intval($_POST['id_user3']) : null;
        $id_user4 = isset($_POST['id_user4']) ? intval($_POST['id_user4']) : null;

        // Preparar la consulta de inserción
        $sql_insert = "INSERT INTO games (id_user, kills, deaths, id_user2, id_user3, id_user4) 
                       VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_insert);
        
        // Verificar si la preparación de la consulta fue exitosa
        if ($stmt === false) {
            echo json_encode([
                "codigo" => 500,
                "mensaje" => "Error al preparar la consulta: " . $conn->error
            ]);
            exit;
        }

        // Vincular los parámetros (los valores de las variables a la consulta)
        $stmt->bind_param("iiiiii", $id_user, $kills, $deaths, $id_user2, $id_user3, $id_user4);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo json_encode([
                "codigo" => 200, // Éxito
                "mensaje" => "Datos insertados correctamente",
                "game_id" => $stmt->insert_id // Devuelve el ID del juego insertado
            ]);
        } else {
            echo json_encode([
                "codigo" => 500,
                "mensaje" => "Error al insertar los datos en la base de datos"
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            "codigo" => 400,
            "mensaje" => "Faltan datos: kills o deaths"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "codigo" => 500,
        "mensaje" => "Error interno en el servidor: " . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

include 'fooder.php';
?>
