<?php

namespace Minus;


/**
 * Response
 */
class Response
{

    /**
     * Les entêtes de la réponse
     *
     * @see headers()
     * @var array
     * @access protected
     */
    protected $headers;

    /**
     * Le corps de la réponse
     *
     * @see body()
     * @var string
     * @access protected
     */
    protected $body;


    public function __construct($headers = array(), $body = '')
    {
        $this->headers($headers);
        $this->body($body);
    }


    /**
     * Getter/Setter des entêtes
     * 
     * @see $headers
     * @param array $headers (optionnel) Les nouvelles entêtes de la réponse
     * @return array
     */
    public function headers($headers = null)
    {
        if (! empty($headers)) {
            $this->headers = $headers;
        }
        return $this->headers;
    }

    /**
     * Getter/Setter du corps
     * 
     * @see $body
     * @param string $body (optionnel) Le nouveau corps de la réponse
     * @return string
     */
    public function body($body = null)
    {
        if (! empty($body)) {
            $this->body = $body;
        }
        return $this->body;
    }


    public function sendHeaders()
    {
        if (empty($this->headers)) {
            return;
        }
        foreach((array) $this->headers as $header) {
            header($header);
        }
    }

    public function printBody()
    {
        print $this->body;
    }

    public function send()
    {
        $this->sendHeaders();
        $this->printBody();
    }

    public function __toString()
    {
        $this->send();
    }

}
