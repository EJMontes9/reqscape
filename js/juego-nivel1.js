let score = 0;

function updateScore(points) {
    score += points;
    if (score < 0) {
        score = 0;
    }
    document.getElementById('score').innerText = 'Puntaje: ' + score;
}

function allowDrop(event) {
    event.preventDefault();
}

function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
}

function drop(event) {
    event.preventDefault();
    var data = event.dataTransfer.getData("text");
    var element = document.getElementById(data);
    if (element) {
        var targetBox = event.target.closest('.box');
        var targetUl = targetBox.querySelector('ul');
        var originalBox = document.getElementById('box2').querySelector('ul');
        targetUl.appendChild(element);

        var isAmbiguous = element.getAttribute('data-ambiguous') === 'true';
        var retro = element.getAttribute('data-retro');
        var targetBoxId = targetBox.id;

        if ((targetBoxId === 'box1' && isAmbiguous) || (targetBoxId === 'box3' && !isAmbiguous)) {
            element.classList.remove('incorrect');
            element.classList.add('correct');
            updateScore(10);
        } else {
            element.classList.remove('correct');
            element.classList.remove('incorrect');
            originalBox.appendChild(element);
            updateScore(-10);
            showModal(retro || "Respuesta incorrecta.");
        }

        // Verificar si no hay más elementos en el box de "Requerimientos"
        if (originalBox.children.length === 0) {
            enviarPuntaje();
        }
    }
}

function showModal(message) {
    var modal = document.getElementById("myModal");
    var modalText = document.getElementById("modal-text");
    var span = document.getElementsByClassName("close")[0];

    modalText.innerText = message;
    modal.style.display = "block";

    span.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

function enviarPuntaje() {
    window.location.href = "resumen-juego.php?score=" + score;
}
