const usuarios = [];

function agregarUsuario(email, password, intereses) {
  usuarios.push({ email, password, intereses });
  console.log("Nuevo usuario registrado:", { email, intereses });
}

module.exports = {
  agregarUsuario,
  usuarios
};