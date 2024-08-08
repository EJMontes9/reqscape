<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReqScape</title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/level-txt.css">
    <style>
        .contenido-juego {
            height: 90%;
        }
        .tooltiptext {
            display: none;
        }
        .fila1-cl1 {
            padding: 7%;
            border-bottom: 1px solid rgba(19, 67, 145, 0.4);
        }
        #languageSelector {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 100%;
            padding: 8%;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: #fff;
            color: #333;
            cursor: pointer;
            outline: none;
            transition: border-color 0.3s ease;
            text-align: center;
            font-family: 'Digitalt';
            font-weight: lighter;
        }
        #languageSelector:focus {
            border-color: #007BFF;
        }
        #languageSelector option {
            color: #333;
            background-color: #fff;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="fondo">
        <div class="columna-1">
            <div class="fila1-cl1">
                <select id="languageSelector">
                    <option value="en">English</option>
                    <option value="es">Español</option>
                </select>
            </div>
            <div class="fila2-cl1">
                <a href="inicio.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/inicio.png" alt="">
                    <span class="tooltiptext" id="home">Home</span>
                </a>
                <a href="niveles-juego.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/niveles.png" alt="">
                    <span class="tooltiptext" id="levels">Levels</span>
                </a>
                <a href="score-page.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/scoreglobal.png" alt="">
                    <span class="tooltiptext" id="score">Score</span>
                </a>
                <a href="perfil.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/perfil.png" alt="">
                    <span class="tooltiptext" id="profile">Profile</span>
                </a>
                <a href="<?php echo ($_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>" class="lg-cl1">
                    <img src="../../assets/img/inicio/<?php echo ($_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>" alt="">
                    <span class="tooltiptext" id="info"><?php echo $translations['info']; ?></span>
                </a>
                <a href="inicio-sesion.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/log-out.png" alt="">
                    <span class="tooltiptext" id="logout">Logout</span>
                </a>
            </div>
        </div>
        <div class="columna-2">
            <div class="fila1-cl2">
                <div class="logo">
                    <img src="../../assets/img/logo.png" alt="">
                </div>
            </div>
            <div class="fila2-cl2">
                <div class="contenido-juego">
                    <div class="fila1">
                        <span class="titulo-nivel-txt" id="level-title">Nivel 01</span>
                    </div>
                    <div class="fila2">
                        <div class="fila2-1">
                            <span class="texto1-txt" id="task-text">
                                ERES UN INGENIERO DE REQUERIMIENTOS Y TE PROPONEN 2 TAREAS QUE TE AYUDARÁN A ASCENDER DE PUESTO…
                            </span>
                        </div>
                        <div class="fila2-2">
                            <button type="button" class="btn-icono" id="skip-button">
                                SALTAR
                                <img src="../../assets/img/lever1-txt/saltar_btn.png" alt="Icono">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../js/cambio-idioma-level1.js"></script>
    <script>
    document.getElementById('skip-button').addEventListener('click', function() {
        window.location.href = 'juego-nivel1.php';
    });
    </script>
</body>
</html>
