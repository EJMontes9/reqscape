<?php
// Asegúrate de que las variables $correo y $token estén definidas antes de usarlas
if (!isset($correo) || empty($correo)) {
    die('Correo electrónico no proporcionado.');
}
if (!isset($token) || empty($token)) {
    die('Token no proporcionado.');
}

// Cargar el autoloader de Composer
require '../../vendor/autoload.php';

// Crear una instancia de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true); // Instancia de PHPMailer

$enviado = false; // Definir la variable antes de usarla

try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'reqscapeseriousgame@gmail.com'; // Tu correo de Gmail
    $mail->Password = 'sqos ukhd foqn ssxn'; // Contraseña de aplicación o la correcta
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Destinatario
    $mail->setFrom('reqscapeseriousgame@gmail.com', 'ReqScape');
    $mail->addAddress($correo); // La dirección de correo del destinatario

    // Contenido del correo
    $codigo = rand(1000, 9999);
    $mail->isHTML(true);
    $mail->Subject = 'Restablecer contraseña ReqScape';
    $mail->Body    = '
    <html>
    <head>
      <title>Restablecer</title>
    </head>
    <body>
        <h1>ReqScape</h1>
        <div style="text-align:center; background-color:#ccc; padding:20px;">
            <p>Restablecer contraseña</p>
            <h3>' . htmlspecialchars($codigo) . '</h3>
            <p><a href="http://localhost/PROGRAMAS/ReqScape/modules/inicio/reset.php?correo=' . urlencode($correo) . '&token=' . urlencode($token) . '">Para restablecer tu contraseña, haz clic aquí</a></p>
            <p><small>Si no solicitaste el restablecimiento de contraseña, ignora este mensaje.</small></p>
        </div>
    </body>
    </html>
    ';

    // Enviar correo
    $mail->send();
    $enviado = true;
    echo 'El correo ha sido enviado.';
} catch (Exception $e) {
    $enviado = false;
    echo 'El correo no pudo ser enviado. Mailer Error: ', $mail->ErrorInfo;
}
?>
