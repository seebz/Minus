<?php

namespace Minus;


/**
 * Inflector
 */
class Inflector
{

    /**
     * Les règles des formes pluriels des mots
     *
     * @see pluralize()
     * @var array
     */
    public static $plural_rules = array(
        '/(quiz)$/i'                   => '\1zes',
        '/^(oxen)$/i'                  => '\1',
        '/^(ox)$/i'                    => '\1en',
        '/^(m|l)ice$/i'                => '\1ice',
        '/^(m|l)ouse$/i'               => '\1ice',
        '/(matr|vert|ind)(?:ix|ex)$/i' => '\1ices',
        '/(x|ch|ss|sh)$/i'             => '\1es',
        '/([^aeiouy]|qu)y$/i'          => '\1ies',
        '/(hive)$/i'                   => '\1s',
        '/(?:([^f])fe|([lr])f)$/i'     => '\1\2ves',
        '/sis$/i'                      => 'ses',
        '/([ti])a$/i'                  => '\1a',
        '/([ti])um$/i'                 => '\1a',
        '/(buffal|tomat)o$/i'          => '\1oes',
        '/(bu)s$/i'                    => '\1ses',
        '/(alias|status)$/i'           => '\1es',
        '/(octop|vir)i$/i'             => '\1i',
        '/(octop|vir)us$/i'            => '\1i',
        '/^(ax|test)is$/i'             => '\1es',
        '/(m)an$/i'                    => '\1en',
        '/s$/i'                        => 's',
        '/$/'                          => 's',
    );

    /**
     * Les règles des formes singulières des mots
     *
     * @see singularize()
     * @var array
     */
    public static $singular_rules = array(
        '/(database)s$/i'              => '\1',
        '/(quiz)zes$/i'                => '\1',
        '/(matr)ices$/i'               => '\1ix',
        '/(vert|ind)ices$/i'           => '\1ex',
        '/^(ox)en/i'                   => '\1',
        '/(alias|status)(es)?$/i'      => '\1',
        '/(octop|vir)(us|i)$/i'        => '\1us',
        '/^(a)x[ie]s$/i'               => '\1xis',
        '/(cris|test)(is|es)$/i'       => '\1is',
        '/(shoe)s$/i'                  => '\1',
        '/(o)es$/i'                    => '\1',
        '/(bus)(es)?$/i'               => '\1',
        '/^(m|l)ice$/i'                => '\1ouse',
        '/(x|ch|ss|sh)es$/i'           => '\1',
        '/(m)ovies$/i'                 => '\1ovie',
        '/(s)eries$/i'                 => '\1eries',
        '/([^aeiouy]|qu)ies$/i'        => '\1y',
        '/([lr])ves$/i'                => '\1f',
        '/(tive)s$/i'                  => '\1',
        '/(hive)s$/i'                  => '\1',
        '/([^f])ves$/i'                => '\1fe',
        '/(^analy)(sis|ses)$/i'        => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)(sis|ses)$/i' => '\1sis',
        '/([ti])a$/i'                  => '\1um',
        '/(n)ews$/i'                   => '\1ews',
        '/(m)en$/i'                    => '\1an',
        '/(ss)$/i'                     => '\1',
        '/s$/i'                        => '',
    );

    /**
     * Liste de mots irréguliers sous la forme `singulier` => `pluriel`
     *
     * @var array
     */
    public static $irregulars = array(
        'person' => 'people',
        'man'    => 'men',
        'child'  => 'children',
        'sex'    => 'sexes',
        'move'   => 'moves',
        'cow'    => 'kine',
        'zombie' => 'zombies',
    );

    /**
     * Liste de mots invariables
     *
     * @var array
     */
    public static $uncountables = array(
        'equipment',
        'information',
        'rice',
        'money',
        'species',
        'series',
        'fish',
        'sheep',
        'jeans',
        'police',
    );

    /**
     * @var array
     * @access protected
     */
    protected static $caches = array();


    /**
     * Converti la chaine `$term` au format CamelCase
     *
     * <code>
     * Inflector::camelize("active_record")               => "ActiveRecord"
     * Inflector::camelize("active_record", false)        => "activeRecord"
     * Inflector::camelize("active_record/errors")        => "ActiveRecord\Errors"
     * Inflector::camelize("active_record/errors", false) => "activeRecord\Errors"
     * </code>
     *
     * @param string $term La chaîne à convertir
     * @param boolean $ucfirst (optionnel) Si spécifié à false, la chaîne sera
     *                         retrournée au format lowerCamelCase
     * @return string
     */
    public static function camelize($term, $ucfirst = true)
    {
        $term = str_replace(array('_', '/'), array(' ', "\t"), $term);
        $term = ucwords($term);
        $term = str_replace(array(' ', "\t"), array('', '\\'), $term);
        if (! $ucfirst) {
            $term[0] = strtolower($term[0]);
        }
        return $term;
    }


    /**
     * Retourne le nom de la classe correspondante à la table `$table_name`
     *
     * <code>
     * Inflector::classify("egg_and_hams") => "EggAndHam"
     * Inflector::classify("posts")        => "Post"
     * </code>
     *
     * @param string $table_name Nom de table
     * @return string
     */
    public static function classify($table_name)
    {
        return static::camelize(static::singularize($table_name));
    }


    /**
     * Replace les underscores `_` par des tirets `-` dans une chaîne
     *
     * <code>
     * Inflector::dasherize("puni_puni") => "puni-puni"
     * </code>
     *
     * @param string $underscored_word
     * @return string
     */
    public static function dasherize($underscored_word)
    {
        return str_replace('_', '-', $underscored_word);
    }


    /**
     * Supprime la partie de module d'une chaîne représantant un nom de classe
     *
     * <code>
     * Inflector::demodulize("String\Inflections") => "Inflections"
     * Inflector::demodulize("Inflections")        => "Inflections"
     * </code>
     *
     * @param string $class_name_in_module
     * @return string
     */
    public static function demodulize($class_name_in_module)
    {
        $str = explode('\\', $class_name_in_module);
        return array_pop($str);
    }


    /**
     * Crée un nom de clé étrangère à partir d'un nom de classe
     *
     * <code>
     * Inflector::foreign_key("Message")        => "message_id"
     * Inflector::foreign_key("Message", false) => "messageid"
     * Inflector::foreign_key("Admin\Post")     => "post_id"
     * </code>
     *
     * @param string $class_name
     * @param boolean $use_underscore
     * @return string
     */
    public static function foreignKey($class_name, $use_underscore = true)
    {
        $out  = static::underscore(static::demodulize($class_name));
        $out .= ($use_underscore ? '_id' : 'id');
        return $out;
    }


    /**
     * Retourne la chaîne `$str` dans un format `Human Readable`
     *
     * <code>
     * Inflector::humanize("employee_salary") => "Employee salary"
     * Inflector::humanize("author_id")       => "Author"
     * </code>
     *
     * @param string $str
     * @return string
     */
    public static function humanize($str)
    {
        $str = str_replace(array('_ids', '_id'), '', $str);
        $str = str_replace('_', ' ', $str);
        return ucfirst(trim($str));
    }


    public static function isUncountable($word)
    {
        return array_search(
            strtolower($word),
            array_map('strtolower', static::$uncountables)
        );
    }


    /**
     * Retourne une chaîne représentant une position correspondante à `$number`
     *
     * <code>
     * Inflector::ordinalize(1)    => "1st"
     * Inflector::ordinalize(2)    => "2nd"
     * Inflector::ordinalize(1002) => "1002nd"
     * Inflector::ordinalize(1003) => "1003rd"
     * </code>
     *
     * @param integer $number
     * @return string
     */
    public static function ordinalize($number)
    {
        if ($number%100<14 && $number%100>10) {
            return $number . 'th';
        } else {
            switch ($number%10) {
                case 1:  return $number . 'st';
                case 2:  return $number . 'nd';
                case 3:  return $number . 'rd';
                default: return $number . 'th';
            }
        }
    }


    /**
     * Retourne la chaîne `$word` au pluriel
     *
     * <code>
     * Inflector::pluralize("post")             => "posts"
     * Inflector::pluralize("octopus")          => "octopi"
     * Inflector::pluralize("sheep")            => "sheep"
     * Inflector::pluralize("words")            => "words"
     * Inflector::pluralize("the blue mailman") => "the blue mailmen"
     * Inflector::pluralize("CamelOctopus")     => "CamelOctopi"
     * </code>
     *
     * @param string $word
     * @return string
     */
    public static function pluralize($word)
    {
        $result = (string) $word;

        if (static::isUncountable($word)) {
            return $word;
        }
        elseif (isset(static::$irregulars[$word])) {
            return static::$irregulars[$word];
        }
        elseif (isset(static::$caches[$word])) {
            return static::$caches[$word];
        }

        foreach (static::$plural_rules as $rule => $replacement) {
            if (preg_match($rule, $result)) {
                $result = preg_replace($rule, $replacement, $result);
                break;
            }
        }

        return static::$caches[$word] = $result;
    }


    /**
     * Retourne la chaîne `$word` au singulier
     *
     * <code>
     * Inflector::singularize("posts")            => "post"
     * Inflector::singularize("octopi")           => "octopus"
     * Inflector::singularize("sheep")            => "sheep"
     * Inflector::singularize("word")             => "word"
     * Inflector::singularize("the blue mailmen") => "the blue mailman"
     * </code>
     *
     * @param string $word
     * @return string
     */
    public static function singularize($word)
    {
        $result = (string) $word;

        if (static::isUncountable($word)) {
            return $word;
        }
        elseif ($singular = array_search($word, static::$irregulars)) {
            return $singular;
        }
        elseif ($plural = array_search($word, static::$caches)) {
            return $plural;
        }

        foreach (static::$singular_rules as $rule => $replacement) {
            if (preg_match($rule, $result)) {
                $result = preg_replace($rule, $replacement, $result);
                break;
            }
        }

        return static::$caches[$result] = $word;
    }


    /**
     * Retourne le nom de la table correspondant à la classe `$class_name`
     *
     * <code>
     * Inflector::tableize("RawScaledScorer") => "raw_scaled_scorers"
     * Inflector::tableize("egg_and_ham")     => "egg_and_hams"
     * Inflector::tableize("fancyCategory")   => "fancy_categories"
     * </code>
     *
     * @param string $class_name
     * @return string
     */
    public static function tableize($class_name)
    {
        return static::pluralize(static::underscore($class_name));
    }


    /**
     * Met en majascule la première lettre de tous les mots et remplace 
     * certains caractères pour produire un meilleur titre
     *
     * <code>
     * Inflector::titleize("man from the boondocks")  => "Man From The Boondocks"
     * Inflector::titleize("x-men: the last stand")   => "X Men: The Last Stand"
     * Inflector::titleize("TheManWithoutAPast")      => "The Man Without A Past"
     * Inflector::titleize("raiders_of_the_lost_ark") => "Raiders Of The Lost Ark"
     * </code>
     *
     * @param string $str
     * @return string
     */
    public static function titleize($str)
    {
        $str = str_replace('-', ' ', $str);
        $str = static::humanize(static::underscore($str));
        $str = ucwords($str);
        return $str;
    }


    /**
     * Converti une chaîne au format `underscore_case`
     *
     * <code>
     * Inflector::underscore("ActiveRecord")         => "active_record"
     * Inflector::underscore("ActiveRecord::Errors") => "active_record/errors"
     * </code>
     *
     * @param string $str
     * @return string
     */
    public static function underscore($str)
    {
        $str = preg_replace('`([A-Z]{1})`', '_\\1', $str);
        $str = strtolower(trim($str, '_'));
        $str = preg_replace('`(::|\\\\+)_?`', '/', $str);
        return $str;
    }

}
