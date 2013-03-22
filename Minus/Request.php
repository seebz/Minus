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
     * Paramètres provenant de la route
     *
     * @see params()
     * @var array
     * @access protected
     */
    protected $params;


    /**
     * Constructeur de la classe
     *
     * @param string @method
     * @param string @path
     */
    public function __construct($path, $method = 'GET')
    {
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
     * @param string $query (optionnel) Le nouveau `query string` de la requête
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
     * Getter/Setter de l'attribut `$params`
     * 
     * @see $params
     * @param array $params (optionnel) Les nouveaux paramètres
     * @return array Valeur de l'attribut `$params`
     */
    public function params($params = null)
    {
        if (! empty($params)) {
            $this->params = $params;
        }
        return (array) $this->params;
    }

    /**
     * Getter d'un paramètre de la route
     *
     * @param string $name Le nom du paramètre à récupérer
     * @param mixed $default (optionnel) Le valeur par défaut à retourner
     *                       si le paramètre n'existe pas
     * @return mixed La valeur du paramètre ou par défaut
     */
    public function param($name, $default = null)
    {
        $params = $this->params();
        return array_key_exists($name, $params) ? $params[$name] : $default;
    }


/*
    public function env($varname)
    {
        $methods = array(
            'scheme'     => 'scheme',
            'user_agent' => 'userAgent',
        );
        if (array_key_exists(strtolower($varname), $methods)) {
            return call_user_func(array($this, $methods[$varname}));
        }
        return getenv($varname);
    }

    public function scheme()
    {
        if (getenv('https') == 'on') {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }
        return $scheme;
    }

    public function userAgent()
    {
        return getenv('HTTP_USER_AGENT');
    }


    public function isAjax()
    {
        $requested_with = $this->env('HTTP_X_REQUESTED_WITH');
        return (
            ! empty($requested_with)
            and strtolower($requested_with) == 'xmlhttprequest'
        );
    }

    public function isCgi()
    {
        return (substr(PHP_SAPI, 0, 3) == 'cgi');
    }

    public function isMobile()
    {
        static $is_mobile;
        if (isset($is_mobile)
            return $is_mobile;

        $user_agent = $this->env('user_agent');
        if (empty($user_agent)) {
            $is_mobile = false;
        } elseif (strpos($user_agent, 'Mobile') !== false
            or strpos($user_agent, 'Android') !== false
            or strpos($user_agent, 'Silk/') !== false
            or strpos($user_agent, 'Kindle') !== false
            or strpos($user_agent, 'BlackBerry') !== false
            or strpos($user_agent, 'Opera Mini') !== false
            or strpos($user_agent, 'Opera Mobi') !== false
        ) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }
        return $is_mobile;
    }

    public function isMsie()
    {
        static $is_msie;
        if (isset($is_msie)
            return $is_msie;

        $user_agent = $this->env('user_agent');
        $is_msie = (strpos($user_agent, 'MSIE') !== false);
        return $is_msie;
    }

    public function isSsl()
    {
        return (getenv('https') == 'on');
    }
*/
}
