    function validarFormulario(event) {
        const nombre = document.getElementById("UserName").value.trim();
        const usuario = document.getElementById("UserDB").value.trim();
        const cedula = document.getElementById("UserCC").value.trim();
        const pass = document.getElementById("Password").value;
        const confirm = document.getElementById("ConfirmPassword").value;

        if (!nombre || !usuario || !cedula || !pass || !confirm) {
            alert("Por favor, completa todos los campos.");
            return false;
        }

        if (pass !== confirm) {
            alert("Las contraseñas no coinciden.");
            return false;
        }

        if (cedula.length > 14 || isNaN(cedula)) {
            alert("La cédula debe ser un número válido de máximo 14 dígitos.");
            return false;
        }

        return true;
    }