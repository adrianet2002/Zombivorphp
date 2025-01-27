<?php
include 'header.php'; 
session_start();

try {
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

    $id = $_SESSION['user_id']; // ID del usuario logueado

    if (isset($_POST['kills']) || isset($_POST['deaths']) || isset($_POST['games_played'])) {
        $kills = isset($_POST['kills']) ? intval($_POST['kills']) : 0; // Si no se envía kills, se asume 0
        $deaths = isset($_POST['deaths']) ? intval($_POST['deaths']) : 0; // Si no se envía deaths, se asume 0
        $gamesPlayed = isset($_POST['games_played']) ? intval($_POST['games_played']) : 0; // Si no se envía games_played, se asume 0

        // Obtener las kills, deaths y games_played actuales del jugador desde la base de datos
        $sql_select = "SELECT kills, deaths, games_played FROM users WHERE id = $id";
        $resultado = $conn->query($sql_select);

        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $killsActuales = intval($row['kills']); // Kills actuales
            $deathsActuales = intval($row['deaths']); // Deaths actuales
            $gamesPlayedActuales = intval($row['games_played']); // Games played actuales

            // Sumar los valores enviados a los actuales
            $killsTotales = $killsActuales + $kills;
            $deathsTotales = $deathsActuales + $deaths;
            $gamesPlayedTotales = $gamesPlayedActuales + $gamesPlayed;

            // Actualizar la base de datos con los nuevos totales
            $sql_update = "UPDATE users SET kills = $killsTotales, deaths = $deathsTotales, games_played = $gamesPlayedTotales WHERE id = $id";
            if ($conn->query($sql_update) === TRUE) {
                echo json_encode([
                    "codigo" => 200, // Éxito
                    "mensaje" => "Datos actualizados correctamente",
                    "killsTotales" => $killsTotales,
                    "deathsTotales" => $deathsTotales,
                    "gamesPlayedTotales" => $gamesPlayedTotales
                ]);
            } else {
                echo json_encode([
                    "codigo" => 500,
                    "mensaje" => "Error al actualizar los datos en la base de datos"
                ]);
            }
        } else {
            echo json_encode([
                "codigo" => 404,
                "mensaje" => "Usuario no encontrado en la base de datos"
            ]);
        }
    } else {
        echo json_encode([
            "codigo" => 400,
            "mensaje" => "Faltan datos: kills, deaths o games_played"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "codigo" => 500,
        "mensaje" => "Error interno en el servidor: " . $e->getMessage()
    ]);
}

include 'fooder.php';
?>
