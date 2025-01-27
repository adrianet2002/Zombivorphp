<?php
// Conexión a la base de datos
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

    // Obtener el userID desde la solicitud
    $userID = isset($_POST['userID']) ? intval($_POST['userID']) : null;

    if ($userID) {
        // Incrementar games_played
        $query = "UPDATE users SET games_played = games_played + 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userID);

        if ($stmt->execute()) {
            echo json_encode([
                "codigo" => 200,
                "mensaje" => "Games played incrementado correctamente."
            ]);
        } else {
            echo json_encode([
                "codigo" => 500,
                "mensaje" => "Error al actualizar: " . $stmt->error
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            "codigo" => 400,
            "mensaje" => "ID de usuario no especificado."
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "codigo" => 500,
        "mensaje" => "Excepción capturada: " . $e->getMessage()
    ]);
}

include 'fooder.php';
?>
