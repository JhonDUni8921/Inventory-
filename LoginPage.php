<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Iniciar Sesión - Inventory+</title>
  <link rel="stylesheet" href="./CSS/StyleLogin.css" />
  <link rel="icon" href="./IMG/Logo.png" type="image/x-icon" />
</head>
<body>

  <section class="form-container">
    <p class="title">INVENTORY +</p>

    <form action="./Data/PaswordTest.php" method="post" onsubmit="return validarFormulario(event)">
      <div>
        <label for="UserName">Nombre de Usuario</label>
        <input type="text" name="UserName" id="UserName" maxlength="16" required />
      </div>

      <div>
        <label for="Password">Contraseña</label>
        <input type="password" name="Password" id="Password" maxlength="20" required />
      </div>

      <button type="submit" class="button-primary">Entrar</button>
    </form>
  </section>

  <section class="section-switch">
    <a href="RegisterPage.php">
      <button type="button">Registrarse</button>
    </a>
  </section>

  <script src="./JS/ValidarLogin.js"></script>
</body>
</html>
