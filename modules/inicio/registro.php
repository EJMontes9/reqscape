<?php
session_start();

include "../../connection/connection.php";

// Verificar si el formulario de registro ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['change_language'])) {
    $usuario = $_POST["userName"];
    $correo = $_POST["usuario"];
    $contraseña = $_POST["password"];
    $perfil = $_POST["perfil"];

    // Hash de la contraseña
    $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);

    try {
        // Verificar si el usuario ya existe en la base de datos
        $stmt = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = ? OR correo = ?");
        $stmt->bind_param('ss', $usuario, $correo);
        $stmt->execute();
        $stmt->bind_result($rowCount);
        $stmt->fetch();
        $stmt->close();

        if ($rowCount > 0) {
            // Mostrar mensaje de error si el usuario ya existe
            echo "<script>alert('El usuario o correo electrónico ya existe');</script>";
        } else {
            // Insertar el nuevo usuario en la base de datos si no existe
            $stmt = $con->prepare("INSERT INTO usuarios (usuario, correo, contrasena, perfil) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $usuario, $correo, $hashedPassword, $perfil);
            $stmt->execute();
            $stmt->close();

            // Mostrar mensaje de éxito
            echo "<script>alert('Usuario creado con éxito');</script>";
        }
    } catch (Exception $e) {
        die("Error al insertar usuario en la base de datos: " . $e->getMessage());
    }
}

// Establecer el idioma
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'es';

// Definir las traducciones
$translations = [
    'es' => [
        'register' => 'Registrarse',
        'user' => 'Usuario',
        'email' => 'Correo',
        'password' => 'Contraseña',
        'profile' => 'Perfil',
        'select_profile' => 'Seleccione su perfil',
        'student' => 'Estudiante',
        'teacher' => 'Profesor',
        'register_now' => 'REGISTRARSE AHORA',
        'login' => 'Iniciar sesión',
        'change_language' => 'Cambiar idioma'
    ],
    'en' => [
        'register' => 'Register',
        'user' => 'User',
        'email' => 'Email',
        'password' => 'Password',
        'profile' => 'Profile',
        'select_profile' => 'Select your profile',
        'student' => 'Student',
        'teacher' => 'Teacher',
        'register_now' => 'REGISTER NOW',
        'login' => 'Login',
        'change_language' => 'Change Language'
    ]
];

$translations = $translations[$lang];

?>


<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title data-translate="register"><?php echo $translations['register']; ?></title>
    <link rel="stylesheet" type="text/css" href="../../styles/registro.css">
    <link rel="stylesheet" href="https://unpkg.com/primeflex@latest/primeflex.css">
</head>
<style>
    .contenedor-login {
        border-radius: 80px;
    }
</style>
<body class="p-0 m-0">
    <div class="w-full h-screen flex justify-content-center align-content-center flex-wrap fondo">
        <div class="h-max contenedor-login">
            <div class="w-full flex justify-content-center flex-wrap">
                <img class="estilo-imagen" src="../../assets/img/inicio/logoinicio.png" alt="no se cargo la imagen">
            </div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="w-full grid mt-3">
                    <div class="w-max col-fixed">
                        <div class="contenedor-icon">
                            <div class="estilo-iconos">
                                <svg class="color-icono" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Icono de usuario -->
                                    <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="col flex align-content-center flex-wrap">
                        <input class="estilo-input px-2" placeholder="<?php echo $translations['user']; ?>" type="text" id="userName" name="userName" required data-translate-placeholder="user_placeholder">
                    </div>
                </div>
                <div class="w-full grid mt-2">
                    <div class="w-max col-fixed">
                        <div class="contenedor-icon">
                            <div class="estilo-iconos">
                                <svg class="color-icono" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Icono de correo -->
                                    <path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="col flex align-content-center flex-wrap">
                        <input class="estilo-input px-2" placeholder="<?php echo $translations['email']; ?>" type="email" id="usuario" name="usuario" required data-translate-placeholder="email_placeholder">
                    </div>
                </div>
                <div class="w-full grid mt-2">
                    <div class="w-max col-fixed">
                        <div class="contenedor-icon">
                            <div class="estilo-iconos">
                                <svg class="color-icono" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Icono de contraseña -->
                                    <path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="col flex align-content-center flex-wrap relative">
                        <input class="estilo-input px-2" placeholder="<?php echo $translations['password']; ?>" required id="passwordID" name="password" type="password" data-translate-placeholder="password_placeholder">
                        <div class="absolute top-0 right-0 mlr-2 mr-3 mt-2 pt-1">
                            <div class="estilo-iconos cursor-pointer" onclick="togglePasswordVisibility()">
                                <svg id="view" xmlns="http://www.w3.org/2000/svg" style="fill:rgb(59, 59, 59); display: block;" viewBox="0 0 576 512"><!-- Icono de ojo abierto -->
                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                </svg>
                                <svg id="no-view" xmlns="http://www.w3.org/2000/svg" style="fill:rgb(59, 59, 59); display: none" viewBox="0 0 640 512"><!-- Icono de ojo cerrado -->
                                    <path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full grid mt-2">
                    <div class="w-max col-fixed">
                        <div class="contenedor-icon">
                            <div class="estilo-iconos">
                                <svg class="color-icono" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icono de perfil -->
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="col flex align-content-center flex-wrap">
                        <select class="estilo-input px-2" id="perfil" name="perfil" required data-translate-placeholder="select_profile">
                            <option value="" disabled selected><?php echo $translations['select_profile']; ?></option>
                            <option value="estudiante" data-translate="student"><?php echo $translations['student']; ?></option>
                            <option value="profesor" data-translate="teacher"><?php echo $translations['teacher']; ?></option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="estilo-button mt-3" data-translate="register_now"><?php echo $translations['register_now']; ?></button>
            </form>
            <form action="inicio-sesion.php" method="GET">
                <button type="submit" class="estilo-button mt-3" data-translate="login"><?php echo $translations['login']; ?></button>
            </form>
            <div class="w-full mt-3 flex justify-content-center">
                <label for="languageSelector"><?php echo $translations['change_language']; ?>:</label>
                <select id="languageSelector" name="language">
                    <option value="es" <?php echo $lang == 'es' ? 'selected' : ''; ?>>Español</option>
                    <option value="en" <?php echo $lang == 'en' ? 'selected' : ''; ?>>English</option>
                </select>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const texts = {
                en: {
                    register: 'Register',
                    user: 'User',
                    email: 'Email',
                    password: 'Password',
                    profile: 'Profile',
                    select_profile: 'Select your profile',
                    student: 'Student',
                    teacher: 'Teacher',
                    register_now: 'REGISTER NOW',
                    login: 'Login',
                    change_language: 'Change Language',
                    user_placeholder: 'User',
                    email_placeholder: 'Email',
                    password_placeholder: 'Password'
                },
                es: {
                    register: 'Registrarse',
                    user: 'Usuario',
                    email: 'Correo',
                    password: 'Contraseña',
                    profile: 'Perfil',
                    select_profile: 'Seleccione su perfil',
                    student: 'Estudiante',
                    teacher: 'Profesor',
                    register_now: 'REGISTRARSE AHORA',
                    login: 'Iniciar sesión',
                    change_language: 'Cambiar idioma',
                    user_placeholder: 'Usuario',
                    email_placeholder: 'Correo',
                    password_placeholder: 'Contraseña'
                }
            };

            const languageSelector = document.getElementById('languageSelector');
            languageSelector.addEventListener('change', changeLanguage);

            function changeLanguage() {
                const selectedLanguage = languageSelector.value;

                document.querySelectorAll('[data-translate]').forEach(element => {
                    const key = element.getAttribute('data-translate');
                    if (texts[selectedLanguage][key]) {
                        element.innerText = texts[selectedLanguage][key];
                    }
                });

                document.querySelectorAll('[data-translate-placeholder]').forEach(element => {
                    const key = element.getAttribute('data-translate-placeholder');
                    if (texts[selectedLanguage][key]) {
                        element.placeholder = texts[selectedLanguage][key];
                    }
                });
            }

            // Set the initial language based on the current selection
            changeLanguage();
        });

        function togglePasswordVisibility() {
            var inputPassword = document.getElementById("passwordID");
            var iconoOjo = document.getElementById("view");
            var iconoOjo2 = document.getElementById("no-view");
            if (inputPassword.type === "password") {
                inputPassword.type = "text";
                iconoOjo.style.display = "none";
                iconoOjo2.style.display = "block";

            } else {
                inputPassword.type = "password";
                iconoOjo2.style.display = "none";
                iconoOjo.style.display = "block";
            }
        }
    </script>
</body>
</html>
