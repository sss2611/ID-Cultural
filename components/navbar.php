<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asegurar que BASE_URL esté definido
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config.php';
}
?>

<!-- Material Icons - CDN -->
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<header class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container d-flex align-items-center justify-content-between">

    <!-- Logo y Nombre -->
    <a href="<?php echo BASE_URL; ?>index.php" class="navbar-brand d-flex align-items-center text-decoration-none">
      <img src="<?php echo BASE_URL; ?>static/img/huella-idcultural.png" alt="ID Cultural Logo" height="40" class="me-2">
      <h4 class="m-0 text-white fw-bold typing-effect" id="navbar-title"></h4>
    </a>

    <div>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <nav class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
          
          <!-- Botón de traducción con dropdown de Bootstrap -->
          <li class="nav-item dropdown">
            <button class="btn btn-link nav-link" id="translateDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Traducir página">
              <i class="bi bi-globe2 fs-5"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="translateDropdown">
              <li><h6 class="dropdown-header"><i class="bi bi-translate"></i> Selecciona un idioma</h6></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('es')">🇪🇸 Español</a></li>
              <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('en')">🇬🇧 English</a></li>
              <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('pt')">🇧🇷 Português</a></li>
              <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('fr')">🇫🇷 Français</a></li>
              <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('it')">🇮🇹 Italiano</a></li>
              <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('de')">🇩🇪 Deutsch</a></li>
            </ul>
          </li>

          <li class="nav-item"><a class="nav-link" href="/index.php">Inicio</a></li>

          <!-- Botón de Búsqueda -->
          <li class="nav-item">
            <button class="btn btn-link nav-link" id="open-search-btn" aria-label="Abrir búsqueda">
              <i class="bi bi-search"></i>
            </button>
          </li>

          <li class="nav-item"><a class="nav-link" href="/wiki.php">Wiki de artistas</a></li>

          <!-- Menú dinámico - Siempre visible pero con opciones diferentes -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="mainMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="gap: 8px;">
              <i class="material-icons" style="font-size: 22px;">view_carousel</i>
              <span>Menú</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-with-icons" aria-labelledby="mainMenuDropdown">
              <?php
                // Determinar si hay sesión y qué rol tiene
                $is_logged_in = isset($_SESSION['user_data']);
                $user_role = $is_logged_in ? $_SESSION['user_data']['role'] : null;
              ?>
              
              <?php if ($is_logged_in && ($user_role === 'artista' || $user_role === 'artista_validado' || $user_role === 'usuario')): ?>
                <!-- Menú para Artistas Logueados -->
                <a href="/index.php" class="dropdown-item">
                  <i class="material-icons">fingerprint</i> Inicio
                </a>
                <a href="/noticias.php" class="dropdown-item">
                  <i class="material-icons">article</i> Noticias
                </a>
                <a href="/perfil_artista.php?id=<?php echo htmlspecialchars($_SESSION['user_data']['id']); ?>" class="dropdown-item">
                  <i class="material-icons">person</i> Mi Perfil
                </a>
                <a href="/src/views/pages/artista/crear-borrador.php" class="dropdown-item">
                  <i class="material-icons">assignment</i> Agregar obras
                </a>
                <a href="/src/views/pages/artista/dashboard-artista.php" class="dropdown-item">
                  <i class="material-icons">build</i> Editar Perfil
                </a>
                <div class="dropdown-divider"></div>
                <a href="/logout.php" class="dropdown-item">
                  <i class="material-icons">exit_to_app</i> Salir
                </a>
              <?php elseif ($is_logged_in && $user_role === 'admin'): ?>
                <!-- Menú para Administradores -->
                <a href="/index.php" class="dropdown-item">
                  <i class="material-icons">fingerprint</i> Inicio
                </a>
                <a href="/noticias.php" class="dropdown-item">
                  <i class="material-icons">article</i> Noticias
                </a>
                <a href="/src/views/pages/admin/dashboard-adm.php" class="dropdown-item">
                  <i class="material-icons">dashboard</i> Panel de Control
                </a>
                <div class="dropdown-divider"></div>
                <a href="/logout.php" class="dropdown-item">
                  <i class="material-icons">exit_to_app</i> Salir
                </a>
              <?php elseif ($is_logged_in && $user_role === 'editor'): ?>
                <!-- Menú para Editores -->
                <a href="/index.php" class="dropdown-item">
                  <i class="material-icons">fingerprint</i> Inicio
                </a>
                <a href="/noticias.php" class="dropdown-item">
                  <i class="material-icons">article</i> Noticias
                </a>
                <a href="/src/views/pages/editor/panel_editor.php" class="dropdown-item">
                  <i class="material-icons">edit</i> Panel de Control
                </a>
                <div class="dropdown-divider"></div>
                <a href="/logout.php" class="dropdown-item">
                  <i class="material-icons">exit_to_app</i> Salir
                </a>
              <?php elseif ($is_logged_in && $user_role === 'validador'): ?>
                <!-- Menú para Validadores -->
                <a href="/index.php" class="dropdown-item">
                  <i class="material-icons">fingerprint</i> Inicio
                </a>
                <a href="/noticias.php" class="dropdown-item">
                  <i class="material-icons">article</i> Noticias
                </a>
                <a href="/src/views/pages/validador/panel_validador.php" class="dropdown-item">
                  <i class="material-icons">verified</i> Panel de Control
                </a>
                <div class="dropdown-divider"></div>
                <a href="/logout.php" class="dropdown-item">
                  <i class="material-icons">exit_to_app</i> Salir
                </a>
              <?php else: ?>
                <!-- Menú para usuarios NO logueados -->
                <a href="/index.php" class="dropdown-item">
                  <i class="material-icons">fingerprint</i> Inicio
                </a>
                <a href="/noticias.php" class="dropdown-item">
                  <i class="material-icons">article</i> Noticias
                </a>
                <div class="dropdown-divider"></div>
                <a href="/src/views/pages/auth/login.php" class="dropdown-item">
                  <i class="material-icons">person_add</i> Iniciar Sesión
                </a>
                <a href="/src/views/pages/auth/registro.php" class="dropdown-item">
                  <i class="material-icons">person_add_alt</i> Crear cuenta
                </a>
              <?php endif; ?>
            </div>
          </li>

          <?php if (isset($_SESSION['user_data'])): ?>
            <!-- Usuario logueado - no mostrar botón adicional, todo está en el menú -->
            <!-- El dropdown menu ya contiene todas las opciones para cada rol -->
            <!-- Usuario logueado - no mostrar botón adicional, todo está en el menú -->
            <!-- El dropdown menu ya contiene todas las opciones para cada rol -->
          <?php else: ?>
            <!-- Se muestra si el usuario NO ha iniciado sesión (invitado) -->
            <!-- Las opciones de login/registro ahora están en el menú dinámico -->
            <!-- Las opciones de login/registro ahora están en el menú dinámico -->
          <?php endif; ?>

        </ul>
      </nav>
    </div>

  </div>
</header>

<!-- Estructura de la ventana de búsqueda -->
<div id="search-overlay" class="search-overlay">
  <button id="close-search-btn" class="btn-close-search" aria-label="Cerrar búsqueda">&times;</button>
  <div class="search-overlay-content">
    <form id="search-form" action="/busqueda.php" method="get">
      <input type="search" name="q" class="form-control-search" placeholder="Buscar artistas, obras, eventos..." autofocus>
      <button type="submit" class="btn-search-submit" aria-label="Buscar">
        <i class="bi bi-search"></i>
      </button>
    </form>
  </div>
</div>

<!-- Google Translate (OCULTO) - Solo para funcionalidad -->
<div id="google_translate_element" style="display: none;"></div>

<script type="text/javascript">
// Función para cambiar el idioma
function changeLanguage(lang) {
  if (lang === 'es') {
    // Volver a español - eliminar traducción
    deleteCookie('googtrans');
    window.location.reload();
  } else {
    // Cambiar a otro idioma
    setCookie('googtrans', '/es/' + lang, 1);
    setCookie('googtrans', '/es/' + lang, 1, window.location.hostname);
    window.location.reload();
  }
}

// Funciones de cookies
function setCookie(name, value, days, domain) {
  let expires = "";
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toUTCString();
  }
  const domainStr = domain ? "; domain=" + domain : "";
  document.cookie = name + "=" + (value || "") + expires + domainStr + "; path=/";
}

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
  return null;
}

function deleteCookie(name) {
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname;
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + window.location.hostname;
}

// Inicializar Google Translate
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'es',
    includedLanguages: 'en,fr,it,de,pt',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
}

// Al cargar la página
window.addEventListener('DOMContentLoaded', function() {
  // Si no hay cookie de traducción, asegurar que esté en español
  const currentLang = getCookie('googtrans');
  if (!currentLang || currentLang === '/es/es' || currentLang === '') {
    deleteCookie('googtrans');
  }
  
  // Inyectar CSS adicional para eliminar barra blanca
  const style = document.createElement('style');
  style.textContent = `
    body {
      top: 0px !important;
      position: static !important;
    }
    .skiptranslate {
      display: none !important;
    }
    body > .skiptranslate {
      display: none !important;
    }
  `;
  document.head.appendChild(style);
});

// Ocultar elementos de Google Translate
window.addEventListener('load', function() {
  setTimeout(() => {
    // Ocultar barra superior
    const frames = document.querySelectorAll('iframe.goog-te-banner-frame');
    frames.forEach(frame => {
      frame.style.display = 'none';
      frame.parentNode && frame.parentNode.removeChild(frame);
    });
    
    // Ajustar el body - CRÍTICO para eliminar la barra blanca
    document.body.style.top = '0px';
    document.body.style.position = 'static';
    document.body.classList.remove('translated-ltr', 'translated-rtl');
    
    // Ocultar el widget
    const widget = document.getElementById('google_translate_element');
    if (widget) widget.style.display = 'none';
    
    // Eliminar divs de Google que causan la barra blanca
    const skiptranslate = document.querySelectorAll('.skiptranslate');
    skiptranslate.forEach(elem => {
      if (elem.tagName === 'DIV' && !elem.querySelector('.goog-te-combo')) {
        elem.style.display = 'none';
      }
    });
  }, 100);
  
  // Segundo intento después de más tiempo
  setTimeout(() => {
    document.body.style.top = '0px';
    document.body.style.position = 'static';
  }, 500);
  
// Tercer intento
setTimeout(() => {
  document.body.style.top = '0px';
  document.body.style.position = 'static';
}, 1000);
});

// Funcionalidad del botón de búsqueda
document.addEventListener('DOMContentLoaded', function() {
const openSearchBtn = document.getElementById('open-search-btn');
const closeSearchBtn = document.getElementById('close-search-btn');
const searchOverlay = document.getElementById('search-overlay');
const searchForm = document.getElementById('search-form');
const searchInput = document.querySelector('.form-control-search');

if (openSearchBtn) {
  openSearchBtn.addEventListener('click', function() {
    searchOverlay.classList.add('active');
    if (searchInput) searchInput.focus();
  });
}

if (closeSearchBtn) {
  closeSearchBtn.addEventListener('click', function() {
    searchOverlay.classList.remove('active');
  });
}

if (searchOverlay) {
  searchOverlay.addEventListener('click', function(e) {
    if (e.target === searchOverlay) {
      searchOverlay.classList.remove('active');
    }
  });
}

if (searchInput) {
  searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Escape') {
      searchOverlay.classList.remove('active');
    }
  });
}

// Efecto de escritura para el título del navbar
const titleElement = document.getElementById('navbar-title');
const fullText = 'ID Cultural';
let currentIndex = 0;

function typeEffect() {
  if (currentIndex < fullText.length) {
    titleElement.textContent = fullText.substring(0, currentIndex + 1) + '|';
    currentIndex++;
    setTimeout(typeEffect, 100); // Velocidad de escritura
  } else {
    // Efecto de cursor parpadeante al final
    let cursorVisible = true;
    setInterval(() => {
      titleElement.textContent = fullText + (cursorVisible ? '|' : '');
      cursorVisible = !cursorVisible;
    }, 500);
  }
}

// Iniciar efecto cuando se carga la página
window.addEventListener('load', typeEffect);
});
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<style>
/* Estilos para Material Icons en el navbar */
.material-icons {
  font-family: 'Material Icons';
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  display: inline-flex;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
  font-feature-settings: 'liga';
  -moz-font-feature-settings: 'liga';
  -moz-osx-font-smoothing: grayscale;
  text-rendering: optimizeLegibility;
  vertical-align: middle;
}

/* Efecto de escritura en el título */
.typing-effect {
  font-family: 'Courier New', monospace;
  letter-spacing: 0.1em;
  min-width: 150px;
  position: relative;
}

.typing-effect::after {
  content: '';
  display: inline;
  animation: blink 0.7s infinite;
  color: white;
}

@keyframes blink {
  0%, 49% {
    opacity: 1;
  }
  50%, 100% {
    opacity: 0;
  }
}

/* Estilos para el dropdown con iconos (Material Icons) */
.dropdown-with-icons {
  min-width: 250px;
  padding: 5px 0;
  border-radius: 4px;
}

.dropdown-with-icons .dropdown-item {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  transition: all 0.2s ease;
  color: #333;
  text-decoration: none;
  font-size: 14px;
  position: relative;
}

.dropdown-with-icons .dropdown-item:hover {
  background-color: rgba(0, 0, 0, 0.04);
  color: #0d6efd;
}

.dropdown-with-icons .dropdown-item i {
  margin-right: 16px;
  font-size: 20px;
  color: #666;
  width: 24px;
  text-align: center;
}

.dropdown-with-icons .dropdown-item:hover i {
  color: #0d6efd;
}

.dropdown-with-icons .dropdown-divider {
  margin: 5px 0;
  background-color: #e9ecef;
}

/* Estilos para Material Icons en el navbar */
.material-icons {
  font-family: 'Material Icons';
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  display: inline-flex;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
  font-feature-settings: 'liga';
  -moz-font-feature-settings: 'liga';
  -moz-osx-font-smoothing: grayscale;
  text-rendering: optimizeLegibility;
  vertical-align: middle;
}

/* Estilos para el dropdown con iconos (Material Icons) */
.dropdown-with-icons {
  min-width: 250px;
  padding: 5px 0;
  border-radius: 4px;
}

.dropdown-with-icons .dropdown-item {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  transition: all 0.2s ease;
  color: #333;
  text-decoration: none;
  font-size: 14px;
  position: relative;
}

.dropdown-with-icons .dropdown-item:hover {
  background-color: rgba(0, 0, 0, 0.04);
  color: #0d6efd;
}

.dropdown-with-icons .dropdown-item i {
  margin-right: 16px;
  font-size: 20px;
  color: #666;
  width: 24px;
  text-align: center;
}

.dropdown-with-icons .dropdown-item:hover i {
  color: #0d6efd;
}

.dropdown-with-icons .dropdown-divider {
  margin: 5px 0;
  background-color: #e9ecef;
}

/* Estilos adicionales para el dropdown de traducción */
.dropdown-menu {
  min-width: 200px;
  z-index: 1100 !important; /* Mayor que search-overlay (1050) */
}

.dropdown-item {
  padding: 0.5rem 1rem;
  transition: background-color 0.2s ease;
  cursor: pointer;
}

.dropdown-item:hover {
  background-color: rgba(0, 123, 255, 0.1);
}

.dropdown-item:active {
  background-color: rgba(0, 123, 255, 0.2);
}

.dropdown-header {
  font-weight: 600;
  color: #0d6efd;
}

/* Estilo del botón de idiomas */
#translateDropdown {
  position: relative;
  padding: 0.5rem;
  transition: all 0.3s ease;
}

#translateDropdown:hover {
  transform: scale(1.1);
  color: rgba(255, 255, 255, 0.9) !important;
}

#translateDropdown .bi-globe2 {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

/* Logo del navbar */
.navbar-brand img {
  filter: brightness(1) contrast(1.0) drop-shadow(0 0 2px rgba(255, 255, 255, 0.8));
  transition: transform 0.3s ease, filter 0.3s ease;
  opacity: 1;
}

.navbar-brand:hover img {
  transform: scale(1.1);
  filter: brightness(1.5) contrast(1.5) drop-shadow(0 0 4px rgba(255, 255, 255, 1));
  opacity: 1;
}

/* Asegurar que NO aparezca la barra de Google */
body > .skiptranslate {
  display: none !important;
}

.goog-te-banner-frame,
.goog-te-banner-frame.skiptranslate {
  display: none !important;
  visibility: hidden !important;
}

#goog-gt-tt, .goog-te-balloon-frame {
  display: none !important;
}

.goog-text-highlight {
  background: none !important;
  box-shadow: none !important;
}

/* Ocultar completamente cualquier elemento de Google Translate */
body.translated-ltr,
body.translated-rtl {
  top: 0 !important;
  margin-top: 0 !important;
}

.goog-te-gadget {
  display: none !important;
}

iframe.skiptranslate {
  display: none !important;
  visibility: hidden !important;
}

.goog-logo-link {
  display: none !important;
}

.goog-te-gadget span {
  display: none !important;
}

#google_translate_element {
  display: none !important;
}
</style>