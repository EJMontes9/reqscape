<?php
include "../../connection/connection.php";

// Configura la zona horaria según tu ubicación
date_default_timezone_set('America/Guayaquil'); // Ajusta la zona horaria según tu ubicación

// Verifica si los datos están definidos antes de usarlos
$correo = isset($_POST['correo']) ? $_POST['correo'] : '';
$token = isset($_POST['token']) ? $_POST['token'] : '';
$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';

// Asegúrate de que las variables no están vacías
if (empty($correo) || empty($token) || empty($codigo)) {
    die('Datos no proporcionados.');
}

// Consulta a la base de datos
$res = $con->query("SELECT * FROM passwords WHERE correo='$correo' AND token='$token' AND codigo=$codigo") or die($con->error);

$correcto = false;
if (mysqli_num_rows($res) > 0) {
    $fila = mysqli_fetch_assoc($res);
    $fecha = $fila['fecha']; // Usa el nombre de la columna en lugar del índice
    $fecha_actual = date("Y-m-d H:i:s"); // Usa el formato de 24 horas
    $seconds = strtotime($fecha_actual) - strtotime($fecha);
    $minutos = $seconds / 60;


    if ($minutos > 10) {
        echo "Token vencido";
    } else {
        echo "";
        $correcto = true;
    }
} else {
    echo "Código incorrecto o vencido";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar password</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row justify-content-md-center" style="margin-top:15%">
            <?php if ($correcto) { ?>
                <form class="col-3" action="./cambiarpassword.php" method="POST">
                    <h2>Restablecer Password</h2>
                    <div class="mb-3">
                        <label for="c" class="form-label">Nuevo Password</label>
                        <input type="password" class="form-control" id="c" name="p1" required>
                    </div>
                    <div class="mb-3">
                        <label for="c" class="form-label">Confirmar Password</label>
                        <input type="password" class="form-control" id="c" name="p2" required>
                        <input type="hidden" class="form-control" id="c" name="correo" value="<?php echo htmlspecialchars($correo); ?>">
                        <input type="hidden" class="form-control" id="c" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        <input type="hidden" class="form-control" id="c" name="codigo" value="<?php echo htmlspecialchars($codigo); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Cambiar</button>
                </form>
            <?php } else { ?>
                <div class="alert alert-danger">Código incorrecto o vencido</div>
            <?php } ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>
