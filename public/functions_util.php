<?php
/**
 * FUNCIONES UTILITARIAS DEL SISTEMA - ITSADATA
 * 
 * Este archivo contiene funciones de utilidad para el manejo seguro
 * de datos, codificación y validación en toda la aplicación.
 */

/**
 * Decodifica una cadena URL
 * @param string $string - Cadena codificada en URL
 * @return string - Cadena decodificada
 */
function eurl($string){
    return urldecode($string);
}

/**
 * Escapa caracteres especiales HTML para prevenir XSS
 * @param string $string - Cadena a escapar
 * @return string - Cadena escapada de forma segura
 */
function eschars($string){
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Convierte caracteres especiales a entidades HTML
 * @param string $string - Cadena a convertir
 * @return string - Cadena con entidades HTML
 */
function ehtml($string){
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Codifica una cadena a JSON de forma segura
 * @param mixed $string - Datos a codificar
 * @return string - JSON codificado de forma segura
 */
function ejson($string){
    return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}

/**
 * Sanitiza una dirección de correo electrónico
 * @param string $string - Email a sanitizar
 * @return string - Email sanitizado
 */
function eemail($string){
    return filter_var($string, FILTER_SANITIZE_EMAIL);
}

/**
 * Genera un hash seguro para contraseñas
 * @param string $string - Contraseña en texto plano
 * @return string - Hash de la contraseña
 */
function epassword($string){
    return password_hash($string, PASSWORD_BCRYPT);
}

/**
 * Verifica si una contraseña coincide con su hash
 * @param string $password - Contraseña en texto plano
 * @param string $hash - Hash almacenado
 * @return bool - True si coincide, false si no
 */
function everify_password($password, $hash){
    return password_verify($password, $hash);
}

?>