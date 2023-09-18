<?php

function __($string, $vars=[]) {
    global $lang;
    
    if (isset($lang[Context::$language_code]) && isset($lang[Context::$language_code][$string])) {
        $string = $lang[Context::$language_code][$string];
    }
    
    foreach ($vars as $key => $value) {
        $string = str_replace("%$key%", $value, $string);
    }
    
    return $string;
}

function genPassword(int $len) {
    
    $result = '';
    
    for ($index = 0; $index < $len; $index++) {
        $result .= substr(
                '_123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ', 
                random_int(0, 58), 
                1);
    }
    
    return $result;
}
