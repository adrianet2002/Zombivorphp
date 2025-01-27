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

    $id_user = intval($_SESSION['user_id']); // ID del usuario logueado

    // Paso 1: Buscar todos los juegos donde aparece el usuario en cualquiera de las columnas
    $query = "
        SELECT id, kills, deaths 
        FROM games 
        WHERE id_user = ? 
        OR id_user2 = ? 
        OR id_user3 = ? 
        OR id_user4 = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo json_encode([
            "codigo" => 500,
            "mensaje" => "Error al preparar la consulta: " . $conn->error
        ]);
        exit;
    }

    // Vincular el ID del usuario a todas las columnas posibles
    $stmt->bind_param("iiii", $id_user, $id_user, $id_user, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Paso 2: Registrar cada juego en la tabla games_history
    $insert_query = "INSERT INTO games_history (users_id, games_id, game_kills, game_deaths)
                     VALUES (?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                     game_kills = VALUES(game_kills),
                     game_deaths = VALUES(game_deaths)";
    $insert_stmt = $conn->prepare($insert_query);
    if ($insert_stmt === false) {
        echo json_encode([
            "codigo" => 500,
            "mensaje" => "Error al preparar la consulta de inserción: " . $conn->error
        ]);
        exit;
    }

    // Insertar o actualizar los datos en games_history
    while ($row = $result->fetch_assoc()) {
        $games_id = $row['id'];
        $game_kills = $row['kills'];
        $game_deaths = $row['deaths'];

        $insert_stmt->bind_param("iiii", $id_user, $games_id, $game_kills, $game_deaths);
        $insert_stmt->execute();
    }

    echo json_encode([
        "codigo" => 200, // Éxito
        "mensaje" => "Se registraron todos los juegos correctamente en games_history"
    ]);

    $stmt->close();
    $insert_stmt->close();
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
