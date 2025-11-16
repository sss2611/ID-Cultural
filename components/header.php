<?php
// Usamos la variable $page_title que definimos en cada página.
// Si no se define, usamos un título por defecto para evitar errores.
$page_title = $page_title ?? 'ID Cultural';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Bootstrap y Tema 'Cosmo' -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/cosmo/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Otras librerías CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- En /components/header.php, dentro del <head> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Favicon y CSS principal (siempre se carga) -->
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>static/img/huella-idcultural.png" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>static/css/main.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>static/css/search.css" />

    <!-- =================================================================== -->
    <!-- Carga de CSS específicos para cada página (si se definen) -->
    <!-- =================================================================== -->
    <?php if (isset($specific_css_files) && is_array($specific_css_files)): ?>
        <?php foreach ($specific_css_files as $css_file): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>static/css/<?php echo htmlspecialchars($css_file); ?>" />
        <?php endforeach; ?>
    <?php endif; ?>
</head>