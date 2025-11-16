document.getElementById("editProfileBtn").addEventListener("click", function(e) {
  e.preventDefault();

  // activar edición en textos
  document.querySelectorAll("[contenteditable]").forEach(el => {
    el.setAttribute("contenteditable", "true");
  });

  // mostrar íconos de lápiz
  document.querySelectorAll(".edit-icon").forEach(icon => {
    icon.style.display = "inline";
  });
});

// cambiar foto al seleccionar archivo
document.getElementById("uploadPic").addEventListener("change", function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(evt) {
      document.getElementById("profilePic").src = evt.target.result;
    };
    reader.readAsDataURL(file);
  }
});

