<?php

namespace Minus;


/**
 * Config
 */
class Config
{

    /**
     * Chemins où chercher les fichiers de configurations
     *
     * @var array
     */
    public static $paths = array();

    /**
     * Configurations chargées
     *
     * @var array
     */
    public static $items = array();


    /**
     * Chargement d'une configuration
     *
     * @param string $file Nom de la configuration à charger
     * @return array|null
     */
    public static function load($file)
    {
        // On nous a fourni un nom de fichier correct
        if (file_exists($file) and is_readable($file)) {
            return static::loadFromFile($file);
        }

        // On recherche le fichier dans les différents `paths` définis
        foreach (static::$paths as $path) {
            $path = realpath($path);
            $src  = $path . '/' . $file;

            if (file_exists($src) and is_readable($src)) {
                return static::loadFromFile($src);
            }

            $found = glob($src . '.*');
            if ($found and is_readable($found[0])) {
                return static::loadFromFile($found[0]);
            }
        }

        return static::$items[$file] = null;
    }

    /**
     * Chargement d'un fichier de configurations
     *
     * @param string $file Nom du fichier à charger
     * @return array|null|false
     */
    public static function loadFromFile($file)
    {
        if (! file_exists($file) or ! is_readable($file)) {
            return false;
        }

        $path_parts = pathinfo($file);
        $group = $path_parts['filename'];

        switch ($path_parts['extension']) {
            case 'ini':
                $config = Parser::parseIniFile($file);
                break;

            case 'json':
                $content = Parser::parseJsonFile($file);
                break;

            case 'php':
                $config = include $file;
                break;

            case 'yaml':
            case 'yml':
                $config = Parser::parserYamlFile($file);
                break;

            case 'xml':
                $config = Parser::parserXmlFile($file);
                break;

            default:
                $config = null;
                break;
        }

        return static::$items[$group] = $config;
    }


    /**
     * Retourne la valeur d'une configuration
     *
     * <code>
     * Config::get('config.item.name');
     * </code>
     *
     * @param string $item    (optional) Le nom de la configuration
     * @param mixed  $default (optional) La valeur par défaut à retourner
     * @return mixed
     */
    public static function get($item = '', $default = null)
    {
        $parts = explode('.', $item);

        // Le groupe existe-t-il ?
        if ($parts[0] and empty(static::$items[$parts[0]])) {
            static::load($parts[0]);
        }

        // On parcoure les éléments à la recherche de celui qui nous intérresse
        $items = static::$items;
        while ($key = array_shift($parts)) {
            if (! is_array($items) or ! array_key_exists($key, $items)) {
                return $default;
            }
            $items = $items[$key];
        }

        return $items;
    }

}
