document.addEventListener('DOMContentLoaded', () => {
  const countdownElement = document.getElementById('countdown');
  const segundos = 7;
  let tiempoRestante = segundos;

  countdownElement.textContent = `Redireccionando en ${tiempoRestante} segundos...`;

  const intervalo = setInterval(() => {
    tiempoRestante--;

    if (tiempoRestante > 0) {
      countdownElement.textContent = `Redireccionando en ${tiempoRestante} segundos...`;
    } else {
      clearInterval(intervalo);
      countdownElement.textContent = "Redireccionando...";
      window.location.href = "/index.php";
    }
  }, 1000);
});
