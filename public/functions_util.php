<?php
function eurl($string){
    return urldecode($string);
}

function eschars($string){
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function ehtml($string){
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function ejson($string){
    return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}
function eemail($string){
    return filter_var($string, FILTER_SANITIZE_EMAIL);
}
function epassword($string){
    return password_hash($string, PASSWORD_BCRYPT);
}
function everify_password($password, $hash){
    return password_verify($password, $hash);
}




?>