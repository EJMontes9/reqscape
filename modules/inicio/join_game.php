<?php
session_start(); // Iniciar la sesión al principio del archivo

include "../../connection/connection.php";

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gamePin = mysqli_real_escape_string($con, $_POST['gamePin']);
    
    // Verificar si el PIN de la sala existe
    $sql = "SELECT * FROM room_requirements1 WHERE room_code = '$gamePin'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // Redirigir a la página de detalles de la sala
        header("Location: ver_codigo_y_puntajes.php?room_code=$gamePin");
        exit();
    } else {
        // Si el PIN no existe, mostrar un mensaje de error
        echo "<script>alert('PIN de juego inválido.'); window.location.href='sala_partida.php';</script>";
    }
}
?>
