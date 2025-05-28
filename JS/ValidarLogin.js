function validarFormulario(event) {
  const user = document.getElementById("UserName").value.trim();
  const pass = document.getElementById("Password").value.trim();

  if (!user || !pass) {
    alert("❗ Por favor completa usuario y contraseña.");
    event.preventDefault();
    return false;
  }
  return true;
}

window.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const error = params.get("error");

  if (error === "usuario") {
    alert("❌ El usuario no está registrado.");
  } else if (error === "clave") {
    alert("⚠️ Contraseña incorrecta.");
  } else if (error === "datos") {
    alert("⚠️ Debes completar todos los campos.");
  }
});
