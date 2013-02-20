<?php

namespace Minus;


/**
 * Route
 */
class Route
{

    /**
     * Patterns par défaut communs à toutes les routes
     * 
     * @var array
     */
    public static $patterns = array(
        'default'    => '[^/]+',
        'lang'       => '[a-z]{2}',
        'controller' => '[^/\.]+',
        'action'     => '[^/\.]+',
        'id'         => '[1-9][0-9]*',
        'page'       => '[1-9][0-9]*',
        'type'       => '[a-z]{2,4}',
    );

    /**
     * Template initial de la route
     * 
     * @var string
     * @access protected
     */
    protected $template = '';

    /**
     * Pattern de la route
     * 
     * @var string
     * @access protected
     */
    protected $pattern = '';

    /**
     * Paramètres de la route
     * 
     * @var array
     * @access protected
     */
    protected $params = array();

    /**
     * Composants de la route sous la forme `nom` => `pattern`
     * 
     * @var array
     * @access protected
     */
    protected $parts = array();


    /**
     * Constructeur de la classe
     *
     * @param string $template Template initial de la route
     * @param array $params Paramètres de la route
     */
    public function __construct($template, $params = array())
    {
        $this->template = $template;
        $this->params = $params;
    }


    /**
     * @see Router\process()
     * @param string $path
     * @return array|false
     */
    public function parse($path)
    {
        if (empty($this->pattern)) {
            $this->compile();
        }
        if (preg_match("`^{$this->pattern}$`", $path, $m)) {
            $params = $this->params;
            foreach ($m as $key => $value) {
                if (intval($key) === $key || in_array($value, array('', '/'))) {
                    continue;
                }
                $params[$key] = $value;
            }
            return $params;
        }
        return false;
    }

    /**
     * @see Router\match()
     * @param string $params
     * @return string|false
     */
    public function match(array $params)
    {
        if (empty($this->pattern)) {
            $this->compile();
        }

        $url = $this->template;

        // On s'assure que les valeurs statiques correspondent
        if (sizeof(array_intersect_assoc($params, $this->params)) !== sizeof($this->params)) {
            return false;
        }

        // Les valeurs restantes
        $p = array_diff_assoc($params, $this->params);
        foreach($p as $k => $v)
        {
            // Pattern
            if (array_key_exists($k, $this->parts)
                and preg_match("`^{$this->parts[$k]}$`", $v)
            ) {
                $url = preg_replace("`\{:{$k}(:[^\}+])?\}`", $v, $url);
                unset($p[$k]);
                continue;
            }
            // Paramètre inconnu ou invalide
            return false;
        }

        // Tout a-t-il bien été traité ?
        if (! empty($p) or strpos($url, '{:') !== false) {
            return false;
        }

        return $url;
    }


    /**
     * @ignore
     */
    protected function compile()
    {
        $this->pattern = $this->template;
        $this->pattern = str_replace('.', '\.', $this->pattern);

        preg_match_all('`{:([^:}]+)}|{:([^}]+):([^}]+)}`', $this->pattern, $m);

        for ($i = 0; $i < count($m[0]); $i++) {
            $n = !empty($m[1][$i]) ? $m[1][$i] : $m[2][$i];
            if (!empty($m[3][$i])) {
                $p = $m[3][$i];
            } elseif (isset(static::$patterns[$n])) {
                $p = static::$patterns[$n];
            } elseif (isset(static::$patterns['default'])) {
                $p = static::$patterns['default'];
            } else {
                $p = '[^/]+';
            }
            $this->parts[$n] = $p;

            $this->pattern = str_replace($m[0][$i], "(?P<{$n}>{$p})", $this->pattern);
        }
    }

}
