<?php

if (!function_exists('stringSnake')) {
    /**
     * 
     */
    function stringSnake($camelCase)
    {
        return preg_replace_callback(
            ["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
            function ($matches) {
                return "_" . lcfirst($matches[0]);
            },
            $camelCase
        );
    }
}

if (!function_exists('arrayKeySnake')) {
    /**
     * String snake to study
     * 
     * @param array $array
     * 
     * @return array
     */
    function arrayKeySnake($array)
    {
        return array_combine(stringSnake(array_keys($array)), $array);
    }
}