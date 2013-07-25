<?php

namespace Minus;


/**
 * Route
 */
class Route
{

    /**
     * Options par défaut des routes
     *
     * @var array
     * @static
     */
    public static $defaults = array(
        'constraints' => array(
            'action'     => '[^/\.]+',
            'controller' => '[^/\.]+',
            'default'    => '(?U).+', // (?U) = ungreedy
            'format'     => '[a-z]{3,4}',
            'id'         => '[1-9][0-9]*',
            'lang'       => '[a-z]{2}',
            'module'     => '(?U).+', // (?U) = ungreedy
            'page'       => '[1-9][0-9]*',
        ),
        'defaults' => array(
            'format' => 'html',
        ),
        'format' => null, // null|true|false
    );


    /**
     * Path initial de la route
     *
     * @see path()
     * @var string
     * @access protected
     */
    protected $path;

    /**
     * Correspondance de la route
     *
     * @see to()
     * @var array
     * @access protected
     */
    protected $to = array();

    /**
     * Options de la route
     *
     * @see options()
     * @var array
     * @access protected
     */
    protected $options;

    /**
     * Pattern PCRE de la route
     *
     * @var string
     * @access protected
     */
    protected $pattern;

    /**
     * Paramètres de la route
     *
     * @see params()
     * @var array
     * @access protected
     */
    protected $params;


    /**
     * Constructeur de la classe
     *
     * @param string $path Path initial de la route
     * @param string|array $to (optionnel) Correspondance de la route
     * @param array $options (optionnel) Options de la route
     */
    public function __construct($path, $to = array(), $options = array())
    {
        if (empty($to) and array_key_exists('to', $options)) {
            $to = $options['to'];
        }

        $this->path($path);
        $this->to($to);
        $this->options($options);

        $this->compile();
    }


    /**
     * Getter/Setter du `path` de la route
     *
     * @param $path string (optionnel) Le nouveau `path` de la route
     * @return string Le `path` de la route
     */
    public function path($path = null)
    {
        if (! empty($path)) {
            $this->path    = ltrim($path, '/');
            $this->pattern = null;
        }

        $path = $this->path;

        // Format
        $format = $this->option('format');
        if (is_null($format)) {
            $path .= '(.:format)'; // optionnel
        } elseif ($format) {
            $path .= '.:format'; // obligatoire
        } else {
            // pas de format
        }

        return $path;
    }

    /**
     * Getter/Setter de la correspondance de la route
     *
     * @param string|array $to (optionnel) La nouvelle correspondance
     * @return array La correspondance de la route
     */
    public function to($to = null)
    {
        if (! is_null($to)) {
            $this->to = $this->convertTo($to);
        }

        return (array) $this->to;
    }

    /**
     * Getter/Setter des options de la route
     *
     * @param array $options (optionnel) Les nouvelles options
     * @return array Les options de la route
     */
    public function options($options = null)
    {
        if (! empty($options)) {
            foreach (static::$defaults as $k => $v) {
                if (! array_key_exists($k, $options)) {
                    $options[$k] = $v;
                } elseif (is_array($v)) {
                    $options[$k] += $v;
                }
            }
            $this->options = $options;
            $this->pattern = null;
        }

        return ($this->options ?: static::$defaults);
    }

    /**
     * Getter d'une option de la route
     *
     * @param string $name Le nom de l'option à récupérer
     * @param mixed $default (optionnel) Le valeur par défaut à retourner
     *                       si l'option n'existe pas
     * @return mixed La valeur de l'option ou par défaut
     */
    public function option($name, $default = null)
    {
        $options = $this->options();
        return array_key_exists($name, $options) ? $options[$name] : $default;
    }

    /**
     * Getter d'une contrainte (masque d'une variable de route)
     *
     * @param string $name Nom de la contrainte
     * @return string Masque correspondant
     */
    public function constraint($name)
    {
        $constraints = (array) $this->option('constraints');
        if (! empty($constraints[$name])) {
            $constraint = $constraints[$name];
        } else {
            $constraint = $constraints['default'];
        }

        return $constraint;
    }

    /**
     * Getter/Setter des paramètres de la route
     *
     * @param array $options (optionnel) Les nouveaux paramètres
     * @return array Les paramètres de la route
     */
    public function params($params = null)
    {
        if (! is_null($params)) {
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
    public function param($name)
    {
        $params = $this->params();
        return isset($params[$name]) ? $params[$name] : null;
    }


    /**
     * Parse un chemin `path` et tente d'en déterminer la correspondance
     *
     * @see Router\process()
     * @param string $path Le chemin à parser
     * @return array|false
     */
    public function parse($path)
    {
        if (empty($this->pattern)) {
            $this->compile();
        }
        $path = '/' . ltrim($path, '/');

        if (preg_match("`^{$this->pattern}$`", $path, $m)) {
            $rules = $this->option('defaults');

            foreach ($m as $key => $value) {
                if (is_numeric($key) or empty($value)) continue;
                $rules[$key] = $value;
            }

            return $this->params = $this->to() + $rules;
        }

        return false;
    }


    /**
     * Traite les paramètres d'une route et tente d'en générer le `path`
     *
     * @see Router\match()
     * @param array Les paramètres de la route à générer
     * @return string|false
     */
    public function match(array $params)
    {
        $url = $this->path();

        // Correspondace des fragments statiques
        if (array_intersect_assoc($params, $this->to) != $this->to) {
            return false;
        }
        $params = array_diff_assoc($params, $this->to);

        // On ignore pour l'instant les valeurs par défaut
        $defaults = $this->option('defaults');
        $params = array_diff_assoc($params, $defaults);

        // Traitement des fragments dynamiques
        foreach ($params as $name => $value) {
            if (strpos($url, ':' . $name) === false) {
                return false; // fragment inconnu
            }
            if (! preg_match('`^' . $this->constraint($name) . '$`', $value)) {
                return false; // fragment invalide (format)
            }
            $url = str_replace(":{$name}", $value, $url);
        }

        // Fragments optionnels
        $pattern = '`[(][^()]*:[a-z]+[^()]*[)]`';
        while (preg_match($pattern, $url)) {
            $url = preg_replace($pattern, '', $url);
        }

        // Valeurs par défaut
        foreach ($defaults as $name => $value) {
            $url = str_replace(":{$name}", $value, $url);
        }

        // Parenthèses restantes
        $url = str_replace(array('(', ')'), '', $url);

        // Derniers test
        if (strpos($url, ':') !== false) {
            return false;
        }

        return '/' . ltrim($url, '/');
    }


    /**
     * Compile le `path` de la route en pattern PCRE
     *
     * @see $pattern
     */
    public function compile()
    {
        $pattern = '/' . $this->path();

        // On protège les parenthèses
        $pattern = str_replace('.', '$.$', $pattern);
        $pattern = str_replace('(', '$($', $pattern);
        $pattern = str_replace(')', '$)$', $pattern);

        // Remplacement des variables par le masque correspondant
        preg_match_all('`(:|\*)([^-$:\*/\(\)]+)`', $pattern, $m);
        foreach ($m[0] as $key => $match) {
            $name       = $m[2][$key];
            $constraint = $this->constraint($name);
            $replace    = "(?P<{$name}>{$constraint})";
            $pattern    = str_replace($match, $replace, $pattern);
        }

        // Les parenthèses sont des fragments optionnels
        $pattern = str_replace('$.$', '\.',  $pattern);
        $pattern = str_replace('$($', '(?:', $pattern);
        $pattern = str_replace('$)$', ')?',  $pattern);

        $this->pattern = $pattern;
    }


    protected function convertTo($to)
    {
        if (is_string($to)) {
            $pattern = '`^'
                . '(?:' . '(?P<module>.+)' . '[\/\\\\])?'
                . '(?P<controller>[^#]+)'
                . '(?:[#]' . '(?P<action>[^(]+)' . ')?'
                . '(?:[(]' . '(?P<arguments>.+)?' . '[)])?'
                . '$`';
            if (preg_match($pattern, $to, $m)) {
                $to = array();
                foreach($m as $k => $v) {
                    if (is_numeric($k) or empty($v)) {
                        continue;
                    }
                    $to[$k] = $v;
                }
            }
        }
        $to = (array) $to;

        if (! empty($to['arguments'])) {
            if (is_string($to['arguments'])) {
                $to['arguments'] = explode(',', $to['arguments']);
            }
            $to['arguments'] = array_map('trim', $to['arguments']);
            $to['arguments'] = array_filter($to['arguments']);

            if (sizeof($to['arguments']) === 1 and empty($to['id'])) {
                $to['id'] = array_shift($to['arguments']);
            }
            if (empty($to['arguments'])) {
                unset($to['arguments']);
            }
        }

        return array_filter($to);
    }

}
