<?php

namespace Minus;


/**
 * Controller
 */
abstract class Controller
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
    public function __construct($request, $response = null)
    {
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
     * Fonction principale du `Controller`, elle est exécutée par le `Dispatcher`
     * et doit retourner la `Response` qui sera envoyée au client.
     * 
     * @return Response
     */
    public function run()
    {
        return $this->response;
    }

}
