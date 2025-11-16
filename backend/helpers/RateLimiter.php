<?php
/**
 * Sistema de Rate Limiting - ID Cultural
 * Limita requests por IP para prevenir abuso
 */

class RateLimiter {
    private static $storageFile = __DIR__ . '/../../storage/rate_limits.json';
    private static $limits = [
        'login' => ['max' => 5, 'window' => 300],              // 5 intentos por 5 min
        'register' => ['max' => 3, 'window' => 3600],          // 3 por hora
        'password_reset' => ['max' => 3, 'window' => 3600],    // 3 por hora
        'api_general' => ['max' => 100, 'window' => 60],       // 100 por minuto
        'search' => ['max' => 30, 'window' => 60],             // 30 búsquedas por minuto
        'upload' => ['max' => 10, 'window' => 3600],           // 10 uploads por hora
    ];

    public static function init() {
        // Crear directorio de storage si no existe
        if (!is_dir(dirname(self::$storageFile))) {
            mkdir(dirname(self::$storageFile), 0755, true);
        }
    }

    /**
     * Verificar si está dentro del límite
     * @param string $action - Tipo de acción (login, register, etc)
     * @return bool - true si está permitido, false si está limitado
     */
    public static function check($action = 'api_general') {
        self::init();

        $ip = self::getClientIp();
        $key = "{$ip}:{$action}";
        $limit = self::$limits[$action] ?? self::$limits['api_general'];

        $data = self::loadData();
        $now = time();

        // Limpiar registros antiguos
        if (!isset($data[$key])) {
            $data[$key] = [];
        }

        $data[$key] = array_filter($data[$key], function($timestamp) use ($now, $limit) {
            return $now - $timestamp < $limit['window'];
        });

        // Verificar si excedió el límite
        if (count($data[$key]) >= $limit['max']) {
            self::saveData($data);
            return false;
        }

        // Registrar nuevo request
        $data[$key][] = $now;
        self::saveData($data);
        return true;
    }

    /**
     * Obtener información de límite actual
     */
    public static function getInfo($action = 'api_general') {
        self::init();

        $ip = self::getClientIp();
        $key = "{$ip}:{$action}";
        $limit = self::$limits[$action] ?? self::$limits['api_general'];

        $data = self::loadData();
        $now = time();

        if (!isset($data[$key])) {
            $data[$key] = [];
        }

        // Limpiar registros antiguos
        $data[$key] = array_filter($data[$key], function($timestamp) use ($now, $limit) {
            return $now - $timestamp < $limit['window'];
        });

        $remaining = $limit['max'] - count($data[$key]);
        $resetIn = !empty($data[$key]) ? $limit['window'] - ($now - min($data[$key])) : 0;

        return [
            'limit' => $limit['max'],
            'remaining' => max(0, $remaining),
            'reset_in' => max(0, $resetIn),
            'reset_at' => date('Y-m-d H:i:s', $now + max(0, $resetIn))
        ];
    }

    /**
     * Agregar headers de rate limit a la respuesta
     */
    public static function addHeaders($action = 'api_general') {
        $info = self::getInfo($action);
        header("X-RateLimit-Limit: {$info['limit']}");
        header("X-RateLimit-Remaining: {$info['remaining']}");
        header("X-RateLimit-Reset: {$info['reset_at']}");
    }

    /**
     * Resetear límite para una acción
     */
    public static function reset($action) {
        self::init();
        $ip = self::getClientIp();
        $key = "{$ip}:{$action}";
        $data = self::loadData();
        unset($data[$key]);
        self::saveData($data);
    }

    /**
     * Resetear todos los límites para una IP
     */
    public static function resetAll() {
        self::init();
        $ip = self::getClientIp();
        $data = self::loadData();

        foreach (array_keys($data) as $key) {
            if (strpos($key, $ip . ':') === 0) {
                unset($data[$key]);
            }
        }

        self::saveData($data);
    }

    /**
     * Obtener IP del cliente
     */
    private static function getClientIp() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return 'UNKNOWN';
    }

    /**
     * Cargar datos de archivo
     */
    private static function loadData() {
        if (!file_exists(self::$storageFile)) {
            return [];
        }

        $data = json_decode(file_get_contents(self::$storageFile), true);
        return is_array($data) ? $data : [];
    }

    /**
     * Guardar datos a archivo
     */
    private static function saveData($data) {
        file_put_contents(self::$storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}

// Inicializar
RateLimiter::init();
