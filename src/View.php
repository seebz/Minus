<?php

namespace minus;


/**
 * View
 */
class View
{

    /**
     * Chemins où chercher les templates
     *
     * @var array
     * @static
     */
    public static $paths = array();

    /** 
     * Extension du fichier
     *
     * @var string
     * @static
     */
    public static $extension = '.php';

    /**
     * Nom de la vue
     *
     * @var string
     * @access protected
     * @static
     */
    protected $template;

    /**
     * Chemin complet du fichier de la vue
     *
     * @see locate()
     * @var string
     * @access protected
     */
    protected $file;

    /**
     * Variables accessibles à la vue
     *
     * @see vars()
     * @var array
     * @access protected
     */
    protected $vars = array();


    public static function addPath($path)
    {
        static::$paths[] = $path;
    }


    /**
     * Constructeur de la classe
     *
     * @param string $template
     * @param array $vars
     */
    public function __construct($template, $vars = array())
    {
        $this->template = $template;
        $this->vars     = $vars;
    }


    /**
     * Getter/Setter de l'attribut `$vars`
     * 
     * @see $vars
     * @param array $vars (optionnel) Le nouvel attribut `$vars`
     * @return array Valeur de l'attribut `$vars`
     */
    public function vars($vars = null)
    {
        if ($vars !== null) {
            $this->vars = (array) $vars;
        }
        return $this->vars;
    }

    /**
     * Génère et retourne la vue
     *
     * @param array $vars (optionnel) Les variables accessibles à la vue
     * @return string
     */
    public function render($vars = array())
    {
        $this->vars = array_merge($vars, (array) $this->vars);
        if ($this->locate())
        {
            extract($this->vars, EXTR_OVERWRITE);
            ob_start();
            include $this->file;
            return ob_get_clean();
        } else {
            $class = Inflector::demodulize(get_class($this));
            throw new \Exception("{$class} {$this->template} not found");
        }
    }

    /**
     * Tente de localiser le fichier correspondant au template
     *
     * @return false|string
     * @access protected
     */
    protected function locate()
    {
        if ($this->file !== null) {
            return $this->file;
        }

        // On nous a fourni le chemin complet
        if (file_exists($this->template) and is_readable($this->template)) {
            return $this->file = $this->template;
        }

        // On détermine les différents chemins à tester
        $locations = $this->locations();

        // On recherche le fichier dans les différentes `locations`
        foreach ($locations as $file) {
            if (file_exists($file) and is_readable($file)) {
                return $this->file = $file;
            }
        }

        // Le template n'a pas été trouvé
        return $this->file = false;
    }

    /**
     * Teste l'existance du fichier correspondant à la vue
     *
     * @return boolean
     */
    public function exists()
    {
        $this->locate();
        return (boolean) $this->file;
    }

    /**
     * Défini les différents fichiers pouvant correspondre à la vue
     *
     * @return array
     * @access protected
     */
    protected function locations()
    {
        $locations = array();
        foreach (static::$paths as $path) {
            $locations[] = $path.'/'.$this->template.static::$extension;
        }

        return $locations;
    }


    public function __get($name)
    {
        return array_key_exists($name, $this->vars) ? $this->vars[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->vars[$name]);
    }

    public function __unset($name)
    {
        unset($this->vars[$name]);
    }

    public function __toString()
    {
        return $this->render();
    }

}
