<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../../styles/recuperarcontra.css">
</head>
<body class="fondo">
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="contenedor-login">
                <form action="./restablecer.php" method="POST">
                    <img class="estilo-imagen" src="../../assets/img/inicio/logoinicio.png" alt="no se cargo la imagen">
                    <h2>Restablecer Password</h2>
                    <div class="mb-3">
                        <label for="c" class="form-label">Correo</label>
                        <input type="email" class="form-control estilo-input" id="c" name="correo" required>
                    </div>
                    <button type="submit" class="btn estilo-button">Restablecer</button>
                    <div class="estilo-sugerencias">
                        <a href="./registro.php">Registrate</a>
                    </div>
                    <div class="estilo-password-olvidado">
                        <a href="./inicio-sesion.php">¿Ya tienes una cuenta? Inicia Sesión</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>
