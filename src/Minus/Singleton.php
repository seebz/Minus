<?php

namespace Minus;


/**
 * Singleton
 */
abstract class Singleton
{

    /**
     * @return Singleton
     */
    public static function instance()
    {
        static $instances = array();

        $class = get_called_class();
        if (! array_key_exists($class, $instances))
        {
            $instanes[$class] = new $class();
        }
        return $instanes[$class];
    }

    protected function __construct() {}
    protected function __clone() {}

}
