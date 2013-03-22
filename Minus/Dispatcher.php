<?php

namespace Minus;

use \Minus\Exception;
use \Minus\Router;


/**
 * Dispatcher
 */
class Dispatcher
{

    /**
     * L'objet `Request` correspondant à la requête
     *
     * @see Request, request()
     * @var Request
     * @access protected
     */
    protected $request;

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
     * @param Request $request (optionnel) L'objet `Request`
     * @param Response $response (optionnel) L'objet `Response`
     */
    public function __construct(
        $request = null,
        $response = null
    ) {
        $this->request($request);
        $this->response($response);
    }


    /**
     * Getter/Setter de l'objet `Request`
     * 
     * @see Request, $request
     * @param Request $request (optionnel) Le nouvel objet `Request`
     * @return Request
     */
    public function request($request = null)
    {
        if (! empty($request)) {
            $this->request = $request;
        }
        return $this->request;
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
     * Fonction principale du `Dispatcher`, elle est chargée d'exécuter le `Controller`
     * et de retourner la `Response` à envoyer au client.
     * 
     * @param Request $request (optionnel) L'objet `Request`
     * @param Response $response (optionnel) L'objet `Response`
     * @return Response
     */
    public function process($request = null, $response = null)
    {
        if (is_null($request)) {
            $request = $this->request;
        }
        if (is_null($response)) {
            $response = $this->response;
        }

        // Route
        if (! $params = Router::process($request->path())) {
            throw new Exception('Route not found',
                Exception::ROUTE_NOT_FOUND);
        }
        $request->params($params);

        // Controller
        $class = Inflector::camelize(
            'app/controller/'
            . (! empty($params['module']) ? $params['modume'] : '')
            . '/' . $params['controller']
        );
        if (! class_exists($class)) {
            throw new Exception(
                'Controller not found',
                Exception::CONTROLLER_NOT_FOUND
            );
        }
        $controller = new $class($request, $response);

        // Action
        $action = Inflector::camelize($params['action'], false);
        $method = array($controller, $action);
        if (! is_callable($method)) {
            throw new Exception(
                'Action not found',
                Exception::ACTION_NOT_FOUND
            );
        }

        // Arguments
        if (! empty($params['id'])) {
            $args = array($params['id']);
        } elseif (! empty($params['arguments'])) {
            $args = (array) $params['arguments'];
        } else {
            $args = array();
        }

        // Réponse
        $ret = call_user_func_array($method, $args);
        return $ret or $response;
    }


    public function loadRoutesFromConfig()
    {
        foreach (Config::get('routes', array()) as $path => $params) {
            if (is_string($params)) {
                $to = $params;
                $options = array();
            } elseif (array_key_exists('to', $params)) {
                $to = $params['to'];
                $options = $params;
                unset($options['to']);
            } else {
                $to = null;
                $options = $params;
            }
            Router::connect($path, $to, $options);
        }
        return $this;
    }

}
