<?php

namespace Minus;


/**
 * Request
 */
class Request
{

    /**
     * Méthode de la requête HTTP
     *
     * @see method()
     * @var string
     * @access protected
     */
    protected $method;

    /**
     * Chemin `path` de la requête
     *
     * @see path()
     * @var string
     * @access protected
     */
    protected $path;

    /**
     * Paramètres `query string` de la requête
     *
     * @see query()
     * @var string
     * @access protected
     */
    protected $query;

    /**
     * L'objet `Route` correspondant à la requête
     *
     * @see Route, route()
     * @var Route
     * @access protected
     */
    protected $route;

    /**
     * L'objet `Response` correspondant à la requête
     *
     * @see Response, response()
     * @var Response
     * @access protected
     */
    protected $response;


    /**
     * Constructeur de la classe
     *
     * @param string $method
     * @param string $path
     */
    public function __construct($path, $method = null)
    {
        if ($method === null) {
            $method = getenv('REQUEST_METHOD');
        }

        $this->path($path);
        $this->method($method);
    }


    /**
     * Getter/Setter de l'attribut `$method`
     * 
     * @see $method
     * @param string $method (optionnel) La nouvelle méthode de la requête
     * @return string Valeur de l'attribut `$method`
     */
    public function method($method = null)
    {
        if (! empty($method)) {
            $this->method = strtoupper($method);
        }
        return $this->method;
    }

    /**
     * Getter/Setter de l'attribut `$path`
     * 
     * @see $path
     * @param string $path (optionnel) Le nouveau path de la requête
     * @return string Valeur de l'attribut `$path`
     */
    public function path($path = null)
    {
        if (! empty($path)) {
            if (strpos($path, '?') !== false) {
                list($path, $query) = explode('?', $path);
                $this->query($query);
            }
            $this->path = $path;
        }
        return $this->path;
    }

    /**
     * Getter/Setter de l'attribut `$query`
     * 
     * @see $query
     * @param string $query (optionnel) Le nouvel attribut `$query`
     * @return string Valeur de l'attribut `$query`
     */
    public function query($query = null)
    {
        if (! empty($query)) {
            $this->query = $query;
        }
        return $this->query;
    }

    /**
     * Getter/Setter de l'objet `Route`
     * 
     * @see Route, $route
     * @param Route $route (optionnel) Le nouvel objet `Route`
     * @return Route
     */
    public function route($route = null)
    {
        if (! empty($route)) {
            $this->route = $route;
        }
        return $this->route;
    }

    /**
     * Getter/Setter de l'objet `Response`
     * 
     * @see Response, $response
     * @param Response $response (optionnel) Le nouvel objet `Response`
     * @return Response
     */
    public function response($response = null)
    {
        if (! empty($response)) {
            $this->response = $response;
        }
        return $this->response;
    }

    /**
     * Exécute la requête et prépare la réponse
     *
     * @return Request
     */
    public function execute()
    {
        // Route
        $this->route = Router::process($this->path());
        if (! $this->route) {
            throw new Exception('Route not found',
                Exception::ROUTE_NOT_FOUND);
        }

        // Controller
        $class = Inflector::camelize(
            'app/controller/'
            . $this->param('module')
            . '/'
            . $this->param('controller')
        );
        if (! class_exists($class)) {
            throw new Exception(
                "Controller `$class` not found",
                Exception::CONTROLLER_NOT_FOUND
            );
        }
        $controller = new $class($this);

        // Action
        $action = Inflector::camelize($this->param('action'), false);
        if (! is_callable(array($controller, $action))) {
            throw new Exception(
                "Action {$class}::{$action} not found",
                Exception::ACTION_NOT_FOUND
            );
        }

        // Arguments
        if ($this->param('id')) {
            $args = array($this->param('id'));
        } elseif ($this->param('arguments')) {
            $args = (array) $this->param('arguments');
        } else {
            $args = array();
        }

        // Réponse
        $this->response = $controller->run($action, $args);

        return $this;
    }


    public function __toString()
    {
        return (string) $this->response;
    }

}
