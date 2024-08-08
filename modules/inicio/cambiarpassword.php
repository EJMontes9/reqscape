<?php 
include "../../connection/connection.php";

// Obtén los datos del formulario
$correo = $_POST['correo'];
$p1 = $_POST['p1'];
$p2 = $_POST['p2'];

// Verifica si las contraseñas coinciden
if ($p1 == $p2) {
    // Encripta la nueva contraseña
    $hashed_password = password_hash($p1, PASSWORD_DEFAULT);

    // Prepara la consulta
    $stmt = $con->prepare("UPDATE usuarios SET contrasena = ? WHERE correo = ?");
    $stmt->bind_param("ss", $hashed_password, $correo);

    // Ejecuta la consulta y verifica el resultado
    if ($stmt->execute()) {
        echo "Contraseña cambiada correctamente";
        // Redirige a la página de inicio de sesión
        header("Location: ./inicio-sesion.php");
        exit();
    } else {
        echo "Error al cambiar la contraseña: " . $stmt->error;
    }
} else {
    echo "Las contraseñas no coinciden";
}
?>
