<?php
/**
 * MultimediaValidator.php - Validación mejorada de multimedia
 * Valida archivos multimedia (imágenes, videos) antes de guardarlos
 */

class MultimediaValidator {
    // Límites de tamaño (en bytes)
    const MAX_IMAGE_SIZE = 10485760;     // 10 MB
    const MAX_VIDEO_SIZE = 524288000;    // 500 MB
    const MAX_AUDIO_SIZE = 104857600;    // 100 MB
    
    // Tipos MIME permitidos (más estricto)
    const ALLOWED_IMAGES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    const ALLOWED_VIDEOS = ['video/mp4', 'video/webm', 'video/quicktime'];
    const ALLOWED_AUDIO = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
    
    // Dimensiones de imagen permitidas
    const MAX_IMAGE_WIDTH = 4000;
    const MAX_IMAGE_HEIGHT = 4000;
    const MIN_IMAGE_WIDTH = 100;
    const MIN_IMAGE_HEIGHT = 100;
    
    /**
     * Obtiene el tipo MIME de un archivo
     */
    private static function getMimeType($file_path) {
        // Intentar usar finfo si está disponible (más confiable)
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file_path);
            finfo_close($finfo);
            return $mime;
        }
        
        // Fallback a mime_content_type si está disponible
        if (function_exists('mime_content_type')) {
            return mime_content_type($file_path);
        }
        
        // Fallback final: obtener del nombre del archivo
        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $mime_types = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg'
        ];
        return $mime_types[$ext] ?? 'application/octet-stream';
    }
    
    /**
     * Valida un archivo de imagen
     * 
     * @param array $file Información del archivo ($_FILES['field'])
     * @return array ['valido' => bool, 'mensaje' => string, 'tamaño' => int]
     */
    public static function validarImagen($file) {
        $respuesta = [
            'valido' => false,
            'mensaje' => '',
            'tamaño' => 0
        ];
        
        // Verificar que el archivo exista
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $respuesta['mensaje'] = 'El archivo no fue subido correctamente';
            return $respuesta;
        }
        
        // Verificar tipo MIME
        $mime = self::getMimeType($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_IMAGES)) {
            $respuesta['mensaje'] = "Tipo de archivo no permitido. Acepta: JPG, PNG, WEBP, GIF (MIME detectado: $mime)";
            return $respuesta;
        }
        
        // Verificar tamaño
        $tamaño = filesize($file['tmp_name']);
        if ($tamaño > self::MAX_IMAGE_SIZE) {
            $respuesta['mensaje'] = "La imagen es muy grande. Máximo: " . self::formatearTamaño(self::MAX_IMAGE_SIZE);
            return $respuesta;
        }
        
        if ($tamaño === 0) {
            $respuesta['mensaje'] = 'El archivo está vacío';
            return $respuesta;
        }
        
        // Verificar dimensiones mínimas de imagen
        $info = getimagesize($file['tmp_name']);
        if ($info === false) {
            $respuesta['mensaje'] = 'No es una imagen válida';
            return $respuesta;
        }
        
        if ($info[0] < 200 || $info[1] < 200) {
            $respuesta['mensaje'] = 'La imagen debe tener mínimo 200x200 píxeles';
            return $respuesta;
        }
        
        $respuesta['valido'] = true;
        $respuesta['tamaño'] = $tamaño;
        return $respuesta;
    }
    
    /**
     * Valida un archivo de video
     */
    public static function validarVideo($file) {
        $respuesta = [
            'valido' => false,
            'mensaje' => '',
            'tamaño' => 0
        ];
        
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $respuesta['mensaje'] = 'El archivo no fue subido correctamente';
            return $respuesta;
        }
        
        $mime = self::getMimeType($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_VIDEOS)) {
            $respuesta['mensaje'] = "Tipo de video no permitido. Acepta: MP4, WEBM, MOV";
            return $respuesta;
        }
        
        $tamaño = filesize($file['tmp_name']);
        if ($tamaño > self::MAX_VIDEO_SIZE) {
            $respuesta['mensaje'] = "El video es muy grande. Máximo: " . self::formatearTamaño(self::MAX_VIDEO_SIZE);
            return $respuesta;
        }
        
        if ($tamaño === 0) {
            $respuesta['mensaje'] = 'El archivo está vacío';
            return $respuesta;
        }
        
        $respuesta['valido'] = true;
        $respuesta['tamaño'] = $tamaño;
        return $respuesta;
    }
    
    /**
     * Valida un archivo de audio
     */
    public static function validarAudio($file) {
        $respuesta = [
            'valido' => false,
            'mensaje' => '',
            'tamaño' => 0
        ];
        
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $respuesta['mensaje'] = 'El archivo no fue subido correctamente';
            return $respuesta;
        }
        
        $mime = self::getMimeType($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_AUDIO)) {
            $respuesta['mensaje'] = "Tipo de audio no permitido. Acepta: MP3, WAV, OGG";
            return $respuesta;
        }
        
        $tamaño = filesize($file['tmp_name']);
        if ($tamaño > self::MAX_AUDIO_SIZE) {
            $respuesta['mensaje'] = "El audio es muy grande. Máximo: " . self::formatearTamaño(self::MAX_AUDIO_SIZE);
            return $respuesta;
        }
        
        if ($tamaño === 0) {
            $respuesta['mensaje'] = 'El archivo está vacío';
            return $respuesta;
        }
        
        $respuesta['valido'] = true;
        $respuesta['tamaño'] = $tamaño;
        return $respuesta;
    }
    
    /**
     * Guarda un archivo multimedia en el servidor
     * 
     * @param array $file Información del archivo
     * @param string $tipo Tipo de archivo (imagen, video, audio)
     * @return array ['exitoso' => bool, 'ruta' => string, 'mensaje' => string]
     */
    public static function guardarArchivo($file, $tipo = 'imagen') {
        $respuesta = [
            'exitoso' => false,
            'ruta' => '',
            'mensaje' => ''
        ];
        
        // Validar según tipo
        if ($tipo === 'imagen') {
            $validacion = self::validarImagen($file);
        } elseif ($tipo === 'video') {
            $validacion = self::validarVideo($file);
        } elseif ($tipo === 'audio') {
            $validacion = self::validarAudio($file);
        } else {
            $respuesta['mensaje'] = 'Tipo de archivo no reconocido';
            return $respuesta;
        }
        
        if (!$validacion['valido']) {
            $respuesta['mensaje'] = $validacion['mensaje'];
            return $respuesta;
        }
        
        // Crear nombre único para el archivo
        $extension = self::obtenerExtension($file['name']);
        $nombre_unico = uniqid('media_', true) . '.' . $extension;
        
        // Definir ruta según tipo
        $directorio_base = __DIR__ . '/../../public/uploads/';
        $subdirectorio = $tipo . 's/';
        $ruta_destino = $directorio_base . $subdirectorio;
        
        // Crear directorio si no existe
        if (!is_dir($ruta_destino)) {
            if (!mkdir($ruta_destino, 0755, true)) {
                $respuesta['mensaje'] = 'No se pudo crear el directorio de destino';
                return $respuesta;
            }
        }
        
        $ruta_completa = $ruta_destino . $nombre_unico;
        
        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $ruta_completa)) {
            $respuesta['mensaje'] = 'Error al guardar el archivo';
            return $respuesta;
        }
        
        // Retornar ruta relativa para BD
        $ruta_relativa = '/uploads/' . $subdirectorio . $nombre_unico;
        
        $respuesta['exitoso'] = true;
        $respuesta['ruta'] = $ruta_relativa;
        $respuesta['mensaje'] = 'Archivo guardado exitosamente';
        
        return $respuesta;
    }
    
    /**
     * Elimina un archivo multimedia
     */
    public static function eliminarArchivo($ruta_relativa) {
        $ruta_completa = __DIR__ . '/../../public' . $ruta_relativa;
        
        if (file_exists($ruta_completa)) {
            return unlink($ruta_completa);
        }
        
        return false;
    }
    
    /**
     * Obtiene la extensión de un archivo
     */
    private static function obtenerExtension($nombre_archivo) {
        $partes = explode('.', $nombre_archivo);
        return strtolower(end($partes));
    }
    
    /**
     * Formatea bytes a formato legible
     */
    private static function formatearTamaño($bytes) {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($unidades) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $unidades[$pow];
    }
}
