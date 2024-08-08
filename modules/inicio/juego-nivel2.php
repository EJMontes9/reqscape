<?php
session_start();

include "../../connection/connection.php";

if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $con->prepare("SELECT usuario, correo, imagen_perfil FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No se encontraron resultados.";
        exit();
    }
} else {
    echo "No se ha iniciado sesión.";
    exit();
}

$solo_mode = false;
if (isset($_GET['room_code'])) {
    $room_code = $_GET['room_code'];
    $_SESSION['room_code'] = $room_code; // Guardar el room_code en la sesión
} else {
    $solo_mode = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nuevo_requerimiento'])) {
    if ($solo_mode) {
        cargarRequerimientosSolo();
    } else {
        cargarRequerimientos($room_code);
    }
    exit;
}

function cargarRequerimientos($room_code) {
    global $con;
    $sql = "SELECT r.id, r.name FROM requirements_2 r
            JOIN room_requirements rr ON r.id = rr.requirement_id
            WHERE rr.room_code = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $room_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $requirements = [];
        while ($row = $result->fetch_assoc()) {
            $requirements[] = $row;
        }

        $requerimientos_data = [];
        foreach ($requirements as $requirement) {
            $requirement_id = $requirement['id'];
            $requirement_name = $requirement['name'];

            $sql_words = "SELECT * FROM palabras WHERE requirements_id = ? ORDER BY requirements_correct, id";
            $stmt_words = $con->prepare($sql_words);
            $stmt_words->bind_param("i", $requirement_id);
            $stmt_words->execute();
            $result_words = $stmt_words->get_result();

            $palabras = [];
            $palabras_correctas = [];
            if ($result_words && $result_words->num_rows > 0) {
                while ($row_words = $result_words->fetch_assoc()) {
                    $palabra = [
                        'texto' => $row_words['palabra'],
                        'correcta' => $row_words['requirements_correct']
                    ];
                    $palabras[] = $palabra;
                    if ($row_words['requirements_correct']) {
                        $palabras_correctas[] = $row_words['palabra'];
                    }
                }
            }

            $requerimientos_data[] = [
                'requirement_text' => $requirement_name,
                'palabras' => $palabras,
                'palabras_correctas' => $palabras_correctas,
                'palabras_correctas_html' => array_map(function($palabra_correcta) {
                    return '<div class="palabra">' . htmlspecialchars($palabra_correcta, ENT_QUOTES, 'UTF-8') . '</div>';
                }, $palabras_correctas)
            ];
        }

        echo json_encode($requerimientos_data);
    } else {
        echo json_encode([
            'error' => 'No se encontraron requerimientos para el código de sala proporcionado.'
        ]);
    }
    $stmt->close();
    $con->close();
}

function cargarRequerimientosSolo() {
    global $con;
    $sql = "SELECT id, name FROM requirements_2 ORDER BY RAND() LIMIT 10";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $requirements = [];
        while ($row = $result->fetch_assoc()) {
            $requirements[] = $row;
        }

        $requerimientos_data = [];
        foreach ($requirements as $requirement) {
            $requirement_id = $requirement['id'];
            $requirement_name = $requirement['name'];

            $sql_words = "SELECT * FROM palabras WHERE requirements_id = ? ORDER BY requirements_correct, id";
            $stmt_words = $con->prepare($sql_words);
            $stmt_words->bind_param("i", $requirement_id);
            $stmt_words->execute();
            $result_words = $stmt_words->get_result();

            $palabras = [];
            $palabras_correctas = [];
            if ($result_words && $result_words->num_rows > 0) {
                while ($row_words = $result_words->fetch_assoc()) {
                    $palabra = [
                        'texto' => $row_words['palabra'],
                        'correcta' => $row_words['requirements_correct']
                    ];
                    $palabras[] = $palabra;
                    if ($row_words['requirements_correct']) {
                        $palabras_correctas[] = $row_words['palabra'];
                    }
                }
            }

            $requerimientos_data[] = [
                'requirement_text' => $requirement_name,
                'palabras' => $palabras,
                'palabras_correctas' => $palabras_correctas,
                'palabras_correctas_html' => array_map(function($palabra_correcta) {
                    return '<div class="palabra">' . htmlspecialchars($palabra_correcta, ENT_QUOTES, 'UTF-8') . '</div>';
                }, $palabras_correctas)
            ];
        }

        echo json_encode($requerimientos_data);
    } else {
        echo json_encode([
            'error' => 'No se encontraron requerimientos.'
        ]);
    }
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordenamiento Requisito No Ambiguo</title>
    <link rel="stylesheet" href="../../styles/juego-nivel2.css">
    <link rel="stylesheet" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/lenguaje.css">
    <style>
        .contenido-juego {
            height: 90%;
        }
        #myModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
            font-family: 'Digitalt';
            font-weight: 100;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 20px;
            width: 40%;
            position: relative;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            background-color: #67EB00;
            border: 2px solid white;
            border-radius: 50%;
            color: white;
            padding: 3% 4%;
        }
        .close:hover {
            background-color: #FF1717;
        }
        .mensaje {
            background-color: #1783ff;
            padding: 2%;
            color: #fefefe;
            font-size: 20px;
            border-radius: 15px;
        }
        .box-ops {
            height: 85%;
            display: grid;
            grid-template-columns: 2fr 1fr;
        }
        .box-requerimientos-correctos {
            margin: 1% 5%;
            height: 285px;
            padding: 5%;
        }
        .box-palabras,
        .box-ops-f2 {
            min-height: 60px;
        }
        .palabra {
            padding: 5px;
            border: 1px solid #ccc;
            margin: 5px;
            cursor: move;
            background-color: #f9f9f9;
            display: inline-block; /* Cambiado a inline-block */
        }
        .over {
            border: 2px dashed #000;
        }
        .requerimiento-correcto {
            margin-bottom: 10px;
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
                    <span class="tooltiptext">Home</span>
                </a>
                <a href="niveles-juego.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/niveles.png" alt="">
                    <span class="tooltiptext">Levels</span>
                </a>
                <a href="score-page.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/scoreglobal.png" alt="">
                    <span class="tooltiptext">Score</span>
                </a>
                <a href="perfil.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/perfil.png" alt="">
                    <span class="tooltiptext">Profile</span>
                </a>
                <a href="informacion.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/info.png" alt="">
                    <span class="tooltiptext">Info</span>
                </a>
                <a href="inicio-sesion.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/log-out.png" alt="">
                    <span class="tooltiptext">Logout</span>
                </a>
            </div>
        </div>
        <div class="columna-2">
            <div class="fila1-cl2">
                <div class="logo">
                    <img src="../../assets/img/logo.png" alt="">
                </div>
                <div class="usuario-logueado">
                    <div class="profile-data-item">
                        <span id="username" class="username-span"><?php echo htmlspecialchars($row["usuario"], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <?php
                    if (!empty($row["imagen_perfil"])) {
                        echo '<img class="profile-pic" src="' . htmlspecialchars($row["imagen_perfil"], ENT_QUOTES, 'UTF-8') . '" alt="Imagen de perfil">';
                    } else {
                        echo '<img class="profile-pic" src="../../modules/inicio/uploads/perfil.jpg" alt="Avatar predeterminado">';
                    }
                    ?>
                </div>
            </div>
            <div class="fila2-cl2">
                <div class="contenido-juego">
                    <div class="titulo-coin">
                        <div class="fila1">
                            <div class="cash" id="score">Puntaje: 0</div>
                            <div class="coin">
                                <img src="../../assets/img/juego-lvl1/coin.png" alt="Coin">
                            </div>
                        </div>
                        <h1 class="fila1-titulo"><span>Nivel 02</span></h1>
                    </div>
                    <div class="box-ops">
                        <div class="box-ops-cl1">
                            <div class="box-ops-f2" id="box-ops-f2">
                                <!-- Aquí se cargarán las palabras -->
                            </div>
                            <div class="box-palabras" id="box-palabras"> </div>
                        </div>

                        <div class="box-requerimientos-correctos" id="box-requerimientos-correctos"></div>
                    </div>

                    <div id="myModal" class="modal">
                        <div class="modal-content">
                            <div class="mensaje">Mensaje</div>
                            <span class="close" id="closeModal">&times;</span>
                            <h2 id="modalTitle">Para Recordar:</h2>
                            <p id="modal-text"></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        let score = 0;

        function updateScore(points) {
            score += points;
            if (score < 0) {
                score = 0;
            }
            document.getElementById('score').innerText = 'Puntaje: ' + score;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const boxOpsF2 = document.getElementById('box-ops-f2');
            const boxPalabras = document.getElementById('box-palabras');
            const boxRequerimientosCorrectos = document.getElementById('box-requerimientos-correctos');
            const modal = document.getElementById('myModal');
            const modalText = document.getElementById('modal-text');
            const closeModal = document.getElementById('closeModal');
            let ordenCorrecto = [];
            let requirementsData = [];
            let currentRequirementIndex = 0;
            let dragSrcEl = null;

            closeModal.onclick = function () {
                modal.style.display = "none";
                cargarNuevoRequerimiento();
            };

            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                    cargarNuevoRequerimiento();
                }
            };

            function handleDragStart(e) {
                dragSrcEl = this;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', this.textContent);
                this.classList.add('dragging');
            }

            function handleDragOver(e) {
                if (e.preventDefault) {
                    e.preventDefault();
                }
                e.dataTransfer.dropEffect = 'move';
                return false;
            }

            function handleDragEnter(e) {
                this.classList.add('over');
            }

            function handleDragLeave(e) {
                this.classList.remove('over');
            }

            function handleDrop(e) {
                if (e.stopPropagation) {
                    e.stopPropagation();
                }

                if (dragSrcEl !== this) {
                    const data = e.dataTransfer.getData('text/plain');
                    const draggedElement = document.querySelector(`.palabra.dragging`);
                    draggedElement.classList.remove('dragging');

                    if (this.parentElement === boxOpsF2 || this.parentElement === boxPalabras) {
                        const targetBox = this.parentElement === boxOpsF2 ? boxOpsF2 : boxPalabras;
                        targetBox.insertBefore(draggedElement, this);
                    } else {
                        boxPalabras.appendChild(draggedElement);
                    }
                }

                verificarOrden();
                return false;
            }

            function handleDragEnd(e) {
                document.querySelectorAll('.palabra').forEach(function (palabra) {
                    palabra.classList.remove('over');
                    palabra.classList.remove('dragging');
                });
            }

            function verificarOrden() {
                const palabrasActuales = Array.from(boxPalabras.children)
                    .map(element => element.textContent);

                if (palabrasActuales.length === ordenCorrecto.length) {
                    const palabrasCorrectas = ordenCorrecto.every((word, index) => word === palabrasActuales[index]);

                    if (palabrasCorrectas) {
                        modalText.textContent = "¡Requerimiento correcto!";
                        modal.style.display = "block";
                        updateScore(10); // Suma 10 puntos si el requerimiento es correcto

                        // Mover el requisito correcto al área de requisitos correctos
                        const requirementElement = document.createElement('div');
                        requirementElement.className = 'requerimiento-correcto';
                        requirementElement.textContent = requirementsData[currentRequirementIndex - 1].requirement_text;
                        boxRequerimientosCorrectos.appendChild(requirementElement);
                    } else {
                        modalText.textContent = "El orden no es correcto.";
                        modal.style.display = "block";
                    }
                }
            }

            function cargarNuevoRequerimiento() {
                if (currentRequirementIndex < requirementsData.length) {
                    const requirement = requirementsData[currentRequirementIndex];
                    boxOpsF2.innerHTML = '';
                    boxPalabras.innerHTML = '';
                    requirement.palabras.forEach(palabra => {
                        const divPalabra = document.createElement('div');
                        divPalabra.classList.add('palabra');
                        divPalabra.setAttribute('draggable', 'true');
                        divPalabra.setAttribute('data-correct', palabra.correcta ? 'true' : 'false');
                        divPalabra.textContent = palabra.texto;
                        boxOpsF2.appendChild(divPalabra);

                        divPalabra.addEventListener('dragstart', handleDragStart, false);
                        divPalabra.addEventListener('dragenter', handleDragEnter, false);
                        divPalabra.addEventListener('dragover', handleDragOver, false);
                        divPalabra.addEventListener('dragleave', handleDragLeave, false);
                        divPalabra.addEventListener('drop', handleDrop, false);
                        divPalabra.addEventListener('dragend', handleDragEnd, false);
                    });
                    ordenCorrecto = requirement.palabras_correctas;
                    currentRequirementIndex++;
                } else {
                    window.location.href = "resumen-juego2.php?score=" + score;
                }
            }

            function cargarRequerimientos() {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', window.location.href, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const data = JSON.parse(xhr.responseText);
                        if (data.error) {
                            console.error('Error:', data.error);
                            return;
                        }

                        requirementsData = data;
                        currentRequirementIndex = 0;
                        cargarNuevoRequerimiento();
                    }
                };
                xhr.send('nuevo_requerimiento=true');
            }

            cargarRequerimientos();

            boxPalabras.addEventListener('dragover', handleDragOver, false);
            boxPalabras.addEventListener('drop', handleDrop, false);
        });

        function finalizarJuego() {
            window.location.href = "resumen-juego2.php?score=" + score;
        }
    </script>
</body>
</html>
