<?php

namespace Minus;


/**
 * Router
 */
class Router
{

    /**
     * Différentes instances de `Route`
     *
     * @see Route
     * @var array
     * @access protected
     */
    protected static $routes = array();


    /**
     * Défini une nouvelle route
     *
     * @see Route
     * @param string $template
     * @param array $params
     * @return array
     */
    public static function connect($template, array $params = array())
    {
        if ($template instanceof Route) {
            static::$routes[] = $template;
        } else {
            static::$routes[] = new Route(compact('template', 'params'));
        }
        return static::$routes;
    }

    /**
     * Traite les différentes routes définies précédement via {@link Router\connect()} à la
     * recherche de celle qui correspond au chemin `path` indiqué en argument. Retourne un tableau
     * contenant les différents éléments récupérés à partir du chemin et des options de la route.
     *
     * @see Route::parse()
     * @param string $path 
     * @return array|false
     */
    public static function process($path)
    {
        $routes = static::$routes;
        foreach ($routes as $route) {
            if ($match = $route->parse($path)) {
                return $match;
            }
        }
        return false;
    }

    /**
     * Tente de générer le chemin `path` correspondant aux critères spécifiés en argument, ceci sur
     * base des différentes routes définies précédement via {@link Router\connect()}.
     *
     * @see Route::match()
     * @param array $params
     * @return string|false
     */
    public static function match(array $params)
    {
        $routes = static::$routes;
        foreach ($routes as $route) {
            if ($url = $route->match($params)) {
                return $url;
            }
        }
        return false;
    }

}
