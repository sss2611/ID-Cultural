<?php
/**
 * Sistema Centralizado de Manejo de Errores - ID Cultural
 * Proporciona respuestas consistentes en JSON y logueo de excepciones
 */

class ErrorHandler {
    private static $errors = [];
    private static $logFile = __DIR__ . '/../../logs/errors.log';
    
    // Códigos de error estándar
    const SUCCESS = 200;
    const CREATED = 201;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const CONFLICT = 409;
    const INTERNAL_ERROR = 500;
    const SERVICE_UNAVAILABLE = 503;

    public static function init() {
        // Crear directorio de logs si no existe
        if (!is_dir(dirname(self::$logFile))) {
            mkdir(dirname(self::$logFile), 0755, true);
        }

        // Configurar manejador de errores
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);

        // Configurar headers JSON
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * Respuesta exitosa
     */
    public static function success($data = null, $message = 'Operación exitosa', $code = self::SUCCESS) {
        self::respond([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Respuesta de error
     */
    public static function error($message, $code = self::INTERNAL_ERROR, $errors = null) {
        self::log('ERROR', $code, $message, $errors);
        
        self::respond([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ], $code);
    }

    /**
     * Error de validación
     */
    public static function validation($errors = [], $message = 'Error de validación') {
        self::log('VALIDATION', self::BAD_REQUEST, $message, $errors);
        
        self::respond([
            'status' => 'error',
            'code' => self::BAD_REQUEST,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ], self::BAD_REQUEST);
    }

    /**
     * Error de autenticación
     */
    public static function unauthorized($message = 'No autorizado') {
        self::log('UNAUTHORIZED', self::UNAUTHORIZED, $message);
        
        self::respond([
            'status' => 'error',
            'code' => self::UNAUTHORIZED,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ], self::UNAUTHORIZED);
    }

    /**
     * Error de permiso
     */
    public static function forbidden($message = 'Acceso prohibido') {
        self::log('FORBIDDEN', self::FORBIDDEN, $message);
        
        self::respond([
            'status' => 'error',
            'code' => self::FORBIDDEN,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ], self::FORBIDDEN);
    }

    /**
     * Recurso no encontrado
     */
    public static function notFound($message = 'Recurso no encontrado') {
        self::log('NOT_FOUND', self::NOT_FOUND, $message);
        
        self::respond([
            'status' => 'error',
            'code' => self::NOT_FOUND,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ], self::NOT_FOUND);
    }

    /**
     * Conflicto (ej: email duplicado)
     */
    public static function conflict($message, $data = null) {
        self::log('CONFLICT', self::CONFLICT, $message);
        
        self::respond([
            'status' => 'error',
            'code' => self::CONFLICT,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], self::CONFLICT);
    }

    /**
     * Manejador de errores PHP
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        self::log('PHP_ERROR', $errno, "$errstr in $errfile:$errline");
        
        if (!(error_reporting() & $errno)) {
            return false;
        }

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Manejador de excepciones
     */
    public static function handleException($exception) {
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();

        self::log('EXCEPTION', $exception->getCode(), "$message at $file:$line", ['trace' => $trace]);

        self::error(
            $message ?: 'Error interno del servidor',
            $exception->getCode() ?: self::INTERNAL_ERROR
        );
    }

    /**
     * Manejador de shutdown (para errores fatales)
     */
    public static function handleShutdown() {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::log('FATAL_ERROR', $error['type'], $error['message'], [
                'file' => $error['file'],
                'line' => $error['line']
            ]);

            self::error('Error fatal del sistema', self::INTERNAL_ERROR);
        }
    }

    /**
     * Registrar en archivo de log
     */
    private static function log($type, $code, $message, $additionalData = null) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = self::getClientIp();
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'N/A';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'N/A';

        $logEntry = "[$timestamp] [$type:$code] $message | IP: $ip | $method $requestUri";

        if ($additionalData) {
            $logEntry .= " | Data: " . json_encode($additionalData);
        }

        $logEntry .= "\n";

        error_log($logEntry, 3, self::$logFile);
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
     * Enviar respuesta JSON
     */
    private static function respond($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

// Inicializar al cargar
ErrorHandler::init();
