<?php

if (!function_exists('stringStudly')) {
    /**
     * String snake to study
     * 
     * @param string|array $string
     * 
     * @return string|array
     */
    function stringStudly($string)
    {
        return preg_replace_callback('/([\_|\-](\w))/', function ($matches) {
            return ucfirst($matches[2]);
        }, $string);
    }
}

if (!function_exists('arrayKeyStudly')) {
    /**
     * String snake to study
     * 
     * @param array $array
     * 
     * @return array
     */
    function arrayKeyStudly($array)
    {
        return array_combine(stringStudly(array_keys($array)), $array);
    }
}