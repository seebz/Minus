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

}
