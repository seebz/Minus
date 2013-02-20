<?php

namespace Minus;


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
     * L'objet `Controller` correspondant à la requête
     * 
     * @see Controller, controller()
     * @var Controller
     * @access protected
     */
    protected $controller;

    /**
     * L'objet `Response` correspondant à la requête
     *
     * @see Response, response()
     * @var Response
     * @access protected
     */
    protected $response;


    /**
     * Closure appelée avant d'exécuter le controller.
     * Peut être un bon moment pour déterminer/charger le controller 
     * correspondant à la requête client.
     */
    public $beforeRun;

    /**
     * Closure appelée après avoir exécuté le controller.
     * Peut être un bon moment pour vérifier/remplacer la réponse.
    */
    public $afterRun;


    /**
     * Constructeur de la classe
     *
     * @param Request @request (optionnel) L'objet `Request`
     * @param Controller @controller (optionnel) L'objet `Controller`
     * @param Response @response (optionnel) L'objet `Response`
     */
    public function __construct(
        $request = null,
        $controller = null,
        $response = null
    ) {
        $this->request($request);
        $this->controller($controller);
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
     * Getter/Setter de l'objet `Controller`
     * 
     * @see Controller, $controller
     * @param Controller $controller (optionnel) Le nouvel objet `Controller`
     * @return Controller
     */
    public function controller($controller = null)
    {
        if (! empty($controller)) {
            $this->controller = $controller;
        }
        return $this->controller;
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
     * @return Response
     */
    public function run()
    {
        // Before run
        if (is_callable($this->beforeRun)) {
            call_user_func($this->beforeRun, $this);
        }

        if (is_callable(array($this->controller, 'run'))) {
            $this->response = $this->controller->run();
        }

        // After run
        if (is_callable($this->afterRun)) {
            call_user_func($this->afterRun, $this);
        }

        return $this->response;
    }

}
