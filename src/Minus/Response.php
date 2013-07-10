<?php

namespace Minus;


/**
 * Response
 */
class Response
{

    /**
     * Liste des status (codes HTTP) et leur message
     *
     * @access public
     * @static
     */
    public static $statuses = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
    );


    /**
     * Status (code HTTP) de la réponse
     *
     * @see status()
     * @var interger
     * @access protected
     */
    protected $status = 200;

    /**
     * Les entêtes de la réponse
     *
     * @see headers()
     * @var array
     * @access protected
     */
    protected $headers = array();

    /**
     * Le corps de la réponse
     *
     * @see body()
     * @var string
     * @access protected
     */
    protected $body;


    /**
     * Constructeur de la classe
     *
     * @param string $body
     * @param integer $status
     * @param array $headers
     */
    public function __construct($body = null, $status = 200, $headers = array())
    {
        $this->body($body);
        $this->status($status);
        $this->headers($headers);
    }


    /**
     * Getter/Setter de l'attribut `$status`
     * 
     * @see $status
     * @param integer $status (optionnel) Le nouvel attribut `$status`
     * @return integer Valeur de l'attribut `$status`
     */
    public function status($status = null)
    {
        if (func_num_args()) {
            if (! array_key_exists($status, static::$statuses)) {
                throw new \InvalidArgumentException("Unknown status type: {$status}");
            }
            $this->status = (int) $status;
        }
        return $this->status;
    }

    /**
     * Getter/Setter de l'attribut `$headers`
     * 
     * @see $headers
     * @param array $headers (optionnel) Le nouvel attribut `$headers`
     * @return array Valeur de l'attribut `$headers`
     */
    public function headers($headers = null)
    {
        if (func_num_args()) {
            $this->headers = (array) $headers;
        }
        return (array) $this->headers;
    }

    /**
     * Getter/Setter de l'attribut `$body`
     * 
     * @see $body
     * @param string $body (optionnel) Le nouvel attribut `$body`
     * @return string Valeur de l'attribut `$body`
     */
    public function body($body = null)
    {
        if (func_num_args()) {
            $this->body = $body;
        }
        return $this->body;
    }

    /**
     * Envoie la réponse
     *
     * @return Response
     */
    public function send()
    {
        $this->sendHeaders();
        $this->printBody();
        return $this;
    }

    /**
     * Émets les headers de la réponse
     *
     * @return Response
     * @access protected
     */
    protected function sendHeaders()
    {
        // Status
        $protocol = getenv('SERVER_PROTOCOL') ?: 'HTTP/1.1';
        $message  = static::$statuses[$this->status];

        header("{$protocol} {$this->status} {$message}", true, $this->status);
        header("Status: {$this->status} {$message}", true, $this->status);

        // Headers
        foreach((array) $this->headers as $name => $value) {
            if (is_string($name)) {
                $value = "{$name}: {$value}";
            }
            header($value, true);
        }
        return $this;
    }

    /**
     * Affiche le contenu du body
     *
     * @return Response
     * @access protected
     */
    protected function printBody()
    {
        print $this->body;
        return $this;
    }


    public function __toString()
    {
        return (string) $this->body;
    }

}
