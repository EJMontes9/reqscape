<?php
// Verificar si se recibió un puntaje válido
if(isset($_POST['score'])) {
    // Obtener el puntaje del formulario
    $score = $_POST['score'];

    // Aquí debes incluir la lógica para guardar el puntaje en la base de datos
    // Por ejemplo, suponiendo que tienes una tabla llamada 'puntajes' con campos 'id', 'usuario_id' y 'puntaje':
    include "../../connection/connection.php"; // Incluimos el archivo de conexión

    // Obtener el ID del usuario logueado (supongamos que está almacenado en una variable de sesión llamada 'user_id')
    session_start();
    $usuario_id = $_SESSION['user_id'];

    // Preparar la consulta SQL para insertar el puntaje
    $sql = "INSERT INTO puntajes (usuario_id, puntaje) VALUES (?, ?)";
    
    // Preparar la declaración
    $stmt = mysqli_prepare($con, $sql);

    // Vincular los parámetros con los valores
    mysqli_stmt_bind_param($stmt, "ii", $usuario_id, $score);

    // Ejecutar la declaración
    mysqli_stmt_execute($stmt);

    // Cerrar la conexión y liberar recursos
    mysqli_stmt_close($stmt);
    mysqli_close($con);

    // Redirigir a la página de resumen del juego o al siguiente nivel
    header("Location: resumen-juego.php");
    exit(); // Terminar el script
} else {
    // Si no se recibió un puntaje válido, redirigir a alguna página de error
    header("Location: error.php");
    exit(); // Terminar el script
}
?>
