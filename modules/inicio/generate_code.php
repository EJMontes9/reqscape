<?php
session_start();
include "../../connection/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_requirements'])) {
    $selectedRequirements = $_POST['selected_requirements'];
    $room_code = 'SALA-' . implode('-', $selectedRequirements);

    foreach ($selectedRequirements as $requirement_id) {
        $sql = "INSERT INTO room_requirements (room_code, requirement_id) VALUES ('$room_code', $requirement_id)";
        if ($con->query($sql) !== TRUE) {
            die("Error al guardar el código: " . $con->error);
        }
    }

    // Redirigir a ver_codigo_y_puntaje.php con el código de la sala
    header("Location: ver_codigo_y_puntaje.php?room_code=$room_code");
    exit();
} else {
    die("No se seleccionaron requerimientos.");
}
?>
