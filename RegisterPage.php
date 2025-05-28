<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrarse - Inventory+</title>
  <link rel="stylesheet" href="./CSS/StyleLogin.css">
  <link rel="icon" href="./IMG/Logo.png" type="image/x-icon">
</head>
<body>

  <section class="form-container form-register">
    <p class="title">Registrarse</p>

    <form action="./Data/RegisterProcess.php" method="post" onsubmit="return validarFormulario(event)">
      <div>
        <label for="UserName">Nombre</label>
        <input type="text" name="UserName" id="UserName" maxlength="20" required>
      </div>

      <div>
        <label for="UserDB">Nombre de Usuario</label>
        <input type="text" name="UserDB" id="UserDB" maxlength="16" required>
      </div>

      <div>
        <label for="UserCC">Cédula</label>
        <input type="number" name="UserCC" id="UserCC" required oninput="this.value = this.value.slice(0, 14);">
      </div>

      <div>
        <label for="Password">Contraseña</label>
        <input type="password" name="Password" id="Password" maxlength="20" required>
      </div>

      <div>
        <label for="ConfirmPassword">Confirmar Contraseña</label>
        <input type="password" name="ConfirmPassword" id="ConfirmPassword" maxlength="20" required>
      </div>

      <button type="submit" class="button-primary">Registrarse</button>
    </form>
  </section>

  <section class="section-switch">
    <a href="LoginPage.php">
      <button type="button">Iniciar Sesión</button>
    </a>
  </section>

  <script src="./JS/ValidarCrear.js"></script>
</body>
</html>
