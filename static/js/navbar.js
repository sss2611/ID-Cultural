window.addEventListener('DOMContentLoaded', () => {
  const navbar = document.getElementById('navbar');
  const footer = document.getElementById('footer');

  if (navbar) {
    fetch("/src/components/navbar.html")
      .then(res => res.text())
      .then(html => {
        navbar.innerHTML = html;
      })
      .catch(err => console.error('Error cargando navbar:', err));
  }

  if (footer) {
    fetch("/src/components/footer.html")
      .then(res => res.text())
      .then(html => {
        footer.innerHTML = html;
      })
      .catch(err => console.error('Error cargando footer:', err));
  }
});
