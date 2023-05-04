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

if (!function_exists('arrayKeyIsNumeric')) {
    /**
     * check array key is numeric
     * 
     * @param array $array
     * 
     * @return array
     */
    function arrayKeyIsNumeric($array)
    {
        if (!is_array($array)) {
            return false;
        }

        if (count($array) <= 0) {
            return true;
        }

        return array_unique(array_map("is_numeric", array_keys($array))) === array(true);
    }
}

if (!function_exists('arrayCombine')) {
    /**
     * two diff length array combine
     * 
     * @param array $array
     * 
     * @return array
     */
    function arrayCombine($arr1, $arr2)
    {
        $count = min(count($arr1), count($arr2));

        return array_combine(array_slice($arr1, 0, $count), array_slice($arr2, 0, $count));
    }
}
