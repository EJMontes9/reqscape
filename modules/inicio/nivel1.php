<?php
session_start();
include "../../connection/connection.php";

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT usuario, correo, imagen_perfil, perfil FROM usuarios WHERE id = $user_id";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "No se encontraron resultados.";
        exit();
    }
} else {
    echo "No se ha iniciado sesión.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_requirements = $_POST['requirements'];
    $room_code = substr(md5(uniqid(rand(), true)), 0, 6); // Generar un código único de 6 caracteres

    foreach ($selected_requirements as $requirement_id) {
        $sql_insert = "INSERT INTO room_requirements (room_code, requirement_id) VALUES ('$room_code', '$requirement_id')";
        mysqli_query($con, $sql_insert);
    }

    echo "Código de sala generado: $room_code";
    exit();
}

$sql_requirements = "SELECT * FROM requirements";
$result_requirements = mysqli_query($con, $sql_requirements);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requerimientos Nivel 1</title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
</head>
<body>
    <h1>Requerimientos Nivel 1</h1>
    <form method="post">
        <table>
            <tr>
                <th>Seleccionar</th>
                <th>ID</th>
                <th>Nombre</th>
                <th>Es Ambiguo</th>
                <th>Retroalimentación</th>
            </tr>
            <?php
            if ($result_requirements && mysqli_num_rows($result_requirements) > 0) {
                while($requirement = mysqli_fetch_assoc($result_requirements)) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='requirements[]' value='" . $requirement['id'] . "'></td>";
                    echo "<td>" . $requirement['id'] . "</td>";
                    echo "<td>" . $requirement['name'] . "</td>";
                    echo "<td>" . ($requirement['is_ambiguous'] ? 'Sí' : 'No') . "</td>";
                    echo "<td>" . $requirement['retro'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No se encontraron requerimientos.</td></tr>";
            }
            ?>
        </table>
        <button type="submit">Generar Código de Sala</button>
    </form>
</body>
</html>

