<?php

namespace PhalconRest;

class Core
{
    public static function array_flatten($array)
    {

        $return = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, self::array_flatten($value));
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }
}
