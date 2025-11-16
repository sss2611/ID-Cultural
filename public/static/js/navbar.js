window.addEventListener('DOMContentLoaded', () => {
  const navbar = document.getElementById('navbar');
  const footer = document.getElementById('footer');

  if (navbar) {
    fetch("/ID-Cultural/components/navbar.php")
      .then(res => res.text())
      .then(html => {
        navbar.innerHTML = "";
        navbar.insertAdjacentHTML("beforeend", html);
        if (window.lucide) lucide.createIcons(); // activa íconos si usás Lucide
      })
      .catch(err => console.error("Error al cargar navbar:", err));
  }

  if (footer) {
    fetch("/ID-Cultural/components/footer.php")
      .then(res => res.text())
      .then(html => {
        footer.innerHTML = "";
        footer.insertAdjacentHTML("beforeend", html);
      })
      .catch(err => console.error("Error al cargar footer:", err));
  }
});