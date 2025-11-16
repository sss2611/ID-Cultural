<?php
session_start();
session_unset();
session_destroy();
// Redirige al usuario a la página de inicio
header('Location: /ID-Cultural/index.php');
exit();
?>