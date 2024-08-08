<?php 
    include "../../connection/connection.php";
    $correo = $_POST['correo'];
    $bytes = random_bytes(5);
    $token = bin2hex($bytes);

    include "mail-reset.php";

    // Estilo CSS embebido para diseño llamativo
    echo '<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: white;
            text-align: center;
            padding: 50px;
            font-size: 0;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-block;
            padding: 30px;
            max-width: 500px;
            margin: auto;
        }
        .header {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>';

    if ($enviado) {
        $con->query("INSERT INTO passwords (correo, token, codigo) VALUES ('$correo', '$token', '$codigo')") or die($con->error);
        echo '
        <div class="container">
            <div class="header">¡Correo Enviado!</div>
            <div class="message">Hemos enviado un correo a <strong>' . htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') . '</strong> con instrucciones para restablecer tu cuenta. Por favor, verifica tu correo para completar el proceso.</div>
            <a href="inicio-sesion.php" class="button">Volver al Inicio</a>
        </div>';
    } else {
        echo '
        <div class="container">
            <div class="header">Error</div>
            <div class="message">Hubo un problema al enviar el correo. Por favor, intenta de nuevo más tarde.</div>
            <a href="inicio-sesion.php" class="button">Volver al Inicio</a>
        </div>';
    }
?>
