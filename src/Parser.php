<?php

namespace minus;


/**
 * Parser
 */
class Parser
{

    /**
     * Décode une chaîne de type CSV
     *
     * @param string $str La chaine à décoder
     * @param boolean $headers (optionnel)
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|false Les données décodées
     */
    public static function parseCsv($str, $headers = false, $obj = false)
    {
        $lines = str_getcsv($str, "\n", '\0');
        $lines = array_filter($lines);

        $sep = ';';
        $result = array();
        foreach ($lines as $i => $line) {
            $data = str_getcsv($line, $sep);

            // `,` est peut-être le séparateur
            if (! $i and sizeof($data) === 1) {
                $test = str_getcsv($line, ',');
                if (sizeof($test) > 1) {
                    $data = $test;
                    $sep = ',';
                }
            }

            $data = array_map('static::castValue', $data);
            if (! $i and $headers) {
                $headers = $data;
            } elseif ($headers) {
                $result[] = array_combine($headers, $data);
            } else {
                $result[] = $data;
            }
        }

        if ($obj) {
            $result = static::arrayToObject($result);
        }
        return $result;
    }

    /**
     * Décode un fichier de type CSV
     *
     * @param string $file Le nom du fichier à décoder
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|false Les données décodées
     */
    public static function parseCsvFile($file, $headers = false, $obj = false)
    {
        $content = static::fileGetContents($file);
        return static::parseCsv($content, $headers, $obj);
    }


    /**
     * Décode une chaîne de type INI
     *
     * @param string $str La chaine à décoder
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|object|false Les données décodées
     */
    public static function parseIni($str, $obj = false)
    {
        $result = parse_ini_string($str, true, INI_SCANNER_RAW);
        $result = static::convertIniResult($result);
        if ($obj) {
            $result = static::arrayToObject($result);
        }
        return $result;
    }

    /**
     * Décode un fichier de type INI
     *
     * @param string $file Le nom du fichier à décoder
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|object|false Les données décodées
     */
    public static function parseIniFile($file, $obj = false)
    {
        $content = static::fileGetContents($file);
        return static::parseIni($content, $obj);
    }


    /**
     * Décode une chaîne JSON
     *
     * @param string $str La chaîne JSON à décoder
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|object|false Les données décodées
     */
    public static function parseJson($str, $obj = false)
    {
        $str = trim($str);
        if ($str[0] !== '{') {
            // Jsonp ?
            $str = preg_replace('`^[^{]*(.*?)[^}]*$`', '$1', $str);
        }
        return json_decode($str, ! $obj);
    }

    /**
     * Décode un fichier JSON
     *
     * @param string $str Le nom du fichier à décoder
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|object|false Les données décodées
     */
    public static function parseJsonFile($str, $obj = false)
    {
        $content = static::fileGetContents($file);
        return static::parseJson($content, $obj);
    }


    /**
     * Décode une requête HTTP
     *
     * @param string $str La chaîne à décoder
     * @param boolean $obj (optionnel) Si vrai, les configurations seront
     *                     retournées en un objet
     * @return array|object|false Les composants de la requête
     */
    public static function parseStr($str, $obj = false)
    {
        parse_str($str, $result);
        if ($obj) {
            $result = static::arrayToObject($result);
        }
        return $result;
    }


    /**
     * Analyse une URL
     *
     * @param string L'url à analyser
     * @param boolean $obj (optionnel) Si vrai, les configurations seront
     *                     retournées en un objet
     * @return array|object|false Les composants de l'URL
     */
    public static function parseUrl($url, $obj = false)
    {
        $result = parse_url($url);
        if ($result) {
            $result += array(
                'scheme'   => null,
                'host'     => null,
                'port'     => null,
                'user'     => null,
                'pass'     => null,
                'path'     => null,
                'query'    => null,
                'fragment' => null,
            );
        }
        if ($obj) {
            $result = static::arrayToObject($result);
        }
        return $result;
    }


    /**
     * Décode une chaîne XML
     * 
     * @param string $str La chaîne XML à décoder
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|object|false Les données décodées
     */
    public static function parseXml($str, $obj = false)
    {
        $xml  = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        return static::parseJson($json, $obj);
    }

    /**
     * Décode un fichier XML
     * 
     * @param string $file Le fichier à décoder
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|object|false Les données décodées
     */
    public static function parseXmlFile($file, $obj = false)
    {
        $content = static::fileGetContents($file);
        return static::parseXml($content, $obj);
    }


    /**
     * Décode une chaîne YAML
     * 
     * @param string $str La chaîne YAML à décoder
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|object|false Les données décodées
     */
    public static function parseYaml($str, $obj = false)
    {
        $result = null;

        // PECL yaml
        if (function_exists('yaml_parse_file')) {
            $result = yaml_parse_file($file);
        }
        // Spyc function
        elseif (function_exists('spyc_load_file')) {
            $result = spyc_load_file($file);
        }
        // Spyc class
        elseif (class_exists('Spyc')
            and is_callable('Spyc::YAMLLoad')
        ) {
            $result = Spyc::YAMLLoad($file);
        }

        if ($obj) {
            $result = static::arrayToObject($result);
        }
        return $result;
    }

    /**
     * Décode un fichier YAML
     * 
     * @param string $file Le fichier à décoder
     * @param boolean $obj (optionnel) Si vrai, les valeurs seront retournées
     *                      sous forme d'objets
     * @return array|object|false Les données décodées
     */
    public static function parserYamlFile($file, $obj = false)
    {
        $content = static::fileGetContents($file);
        return static::parseYaml($content, $obj);
    }


    protected static function convertIniResult($input)
    {
        if (! $input) {
            return $input;
        }
        $output = array();

        // Convertion du tableau
        $input = static::convertIniArray($input);

        // On gère l'héritage/surcharge
        foreach ($input as $key => $value) {
            if (strpos($key, ':') !== false) {
                list($key_child, $key_parent) = explode(':', $key);
                $key_parent = trim($key_parent);
                $key_child  = trim($key_child);

                if (array_key_exists($key_parent, $output)) {
                    $key   = $key_child;
                    $value = array_merge($output[$key_parent], $value);
                }
            }
            $output[$key] = $value;
        }

        return $output;
    }

    protected static function convertIniArray($input)
    {
        if (is_array($input)) {
            $output = array();
            foreach ($input as $key => $value) {

                if (strpos($key, '.') !== false) {
                    list($key_parent, $key_child) = explode('.', $key, 2);
                    if (! array_key_exists($key_parent, $output)) {
                        $output[$key_parent] = array();
                    }
                    $value = array($key_child => $value);
                    $value = call_user_func(__METHOD__, $value);
                    $output[$key_parent] += $value;
                }
                else {
                    $value = call_user_func(__METHOD__, $value);
                    $output[$key] = $value;
                }
            }
            return $output;
        }
        else {
            return self::castValue($input);
        }
    }

    protected static function castValue($input)
    {
        $output = $input;

        if (! is_string($input)) {
            return $output;
        }

        $lower = strtolower($input);
        if ($lower === 'null') {
            $output = null;
        }
        elseif (in_array($lower, array('no', 'false', 'off'), true)) {
            $output = false;
        }
        elseif (in_array($lower, array('yes', 'true', 'on'), true)) {
            $output = true;
        }
        elseif (is_numeric($input)) {
            $numeric = $input + 0;
            if ('' . $numeric === $input) {
                $output = $numeric;
            }
        }

        return $output;
    }

    protected static function fileGetContents($file)
    {
        $content = file_get_contents($file);
        return $content;
    }

    protected static function arrayToObject($input)
    {
        return json_decode(json_encode($input));
    }

}
