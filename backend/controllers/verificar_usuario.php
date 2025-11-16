<?php
// backend/controllers/verificar_usuario.php
// Este archivo contiene la lógica encapsulada en una función.

// NO INICIES LA SESIÓN AQUÍ SI ESTE ARCHIVO VA A SER INCLUIDO EN OTRO.
// session_start(); // <--- ELIMINAR ESTA LÍNEA AQUÍ. La iniciará el script que lo incluya.

// La ruta a connection.php desde backend/controllers/
require_once __DIR__ . '/../config/connection.php';

/**
 * Verifica las credenciales de un usuario o artista.
 * @param string $email El correo electrónico del usuario.
 * @param string $password La contraseña del usuario (texto plano).
 * @return array Un array asociativo con 'status', 'role', 'redirect' y 'message'.
 */
function checkUserCredentials($email, $password) {
    global $pdo; // Accede a la conexión PDO desde connection.php

    if (!$email || !$password) {
        return [
            "status" => "error",
            "message" => "Faltan datos de acceso"
        ];
    }

    $user = null;
    $origen = null;

    // Buscar primero en users
    $stmtUser = $pdo->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
    $stmtUser->execute([$email]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $origen = 'users';
    }

    // Si no se encontró en users, buscar en artistas
    if (!$user) {
        $stmtArtista = $pdo->prepare("SELECT id, email, password, nombre, apellido FROM artistas WHERE email = ?");
        $stmtArtista->execute([$email]);
        $artista = $stmtArtista->fetch(PDO::FETCH_ASSOC);

        if ($artista) {
            $artista['role'] = 'artista'; // Asignar el rol 'artista' explícitamente
            $user = $artista;
            $origen = 'artistas';
        }
    }

    // Verificación de contraseña
    if ($user && password_verify($password, $user['password'])) {
        // La sesión se manejará en el script que llama a esta función (public/api/login.php)
        // $_SESSION['user'] = $user; // <--- NO ASIGNAR AQUÍ. Devolver los datos y que el llamador lo haga.

        // Determinar la URL de redirección (relativa al DocumentRoot 'public/')
        $redirect = '';
        switch ($user['role']) {
            case 'admin':
                $redirect = '/src/views/pages/admin/dashboard-adm.php';
                break;
            case 'editor':
                $redirect = '/src/views/pages/editor/panel_editor.php';
                break;
            case 'validador':
                $redirect = '/src/views/pages/validador/panel_validador.php';
                break;
            case 'artista':
                $redirect = '/src/views/pages/artista/dashboard-artista.php';
                break;
            default:
                $redirect = '/src/views/pages/public/home.php'; // Asumiendo que home.php es la landing pública
                break;
        }

        return [
            "status" => "ok",
            "role" => $user['role'],
            "redirect" => $redirect, // Devolvemos la URL para que JS redirija
            "source" => $origen,
            "user_data" => [ // Devolver datos del usuario completos (sin password)
                'id' => $user['id'],
                'email' => $user['email'],
                'nombre' => $user['nombre'] ?? '',
                'apellido' => $user['apellido'] ?? '',
                'role' => $user['role']
            ]
        ];
    } else {
        return [
            "status" => "error",
            "message" => "Usuario o contraseña incorrectos"
        ];
    }
}
?>