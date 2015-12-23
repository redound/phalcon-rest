<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use PhalconRest\Constants\HttpMethods;

class CorsMiddleware extends \PhalconRest\Mvc\Plugin
{
    const ALL_ORIGINS = ['*'];
    const DEFAULT_HEADERS = ['Content-Type', 'X-Requested-With', 'Authorization'];

    /**
     * @var array Allowed origins
     */
    protected $_allowedOrigins;

    /**
     * @var array Allowed methods
     */
    protected $_allowedMethods;

    /**
     * @var array Allowed headers
     */
    protected $_allowedHeaders;


    /**
     * Cors constructor.
     *
     * @param array|null $allowedOrigins Allowed origins
     * @param array|null $allowedMethods Allowed methods
     * @param array|null $allowedHeaders Allowed headers
     */
    public function __construct(array $allowedOrigins = self::ALL_ORIGINS, array $allowedMethods = HttpMethods::ALL_METHODS, array $allowedHeaders = self::DEFAULT_HEADERS)
    {
        $this->setAllowedOrigins($allowedOrigins);
        $this->setAllowedMethods($allowedMethods);
        $this->setAllowedHeaders($allowedHeaders);
    }


    public function getAllowedOrigins()
    {
        return $this->_allowedOrigins;
    }

    public function setAllowedOrigins(array $allowedOrigins)
    {
        if($allowedOrigins === null){
            $allowedOrigins = [];
        }

        $this->_allowedOrigins = $allowedOrigins;
    }

    public function addAllowedOrigin($origin)
    {
        $this->_allowedOrigins[] = $origin;
    }


    public function getAllowedMethods()
    {
        return $this->_allowedMethods;
    }

    public function setAllowedMethods(array $allowedMethods)
    {
        if($allowedMethods === null){
            $allowedMethods = [];
        }

        $this->_allowedMethods = $allowedMethods;
    }

    public function addAllowedMethod($method)
    {
        $this->_allowedMethods[] = $method;
    }


    public function getAllowedHeaders()
    {
        return $this->_allowedHeaders;
    }

    public function setAllowedHeaders(array $allowedHeaders)
    {
        if($allowedHeaders === null){
            $allowedHeaders = [];
        }

        $this->_allowedHeaders = $allowedHeaders;
    }

    public function addAllowedHeader($header)
    {
        $this->_allowedHeaders[] = $header;
    }


    public function beforeExecuteRoute(Event $event, \PhalconRest\Api $api)
    {
        if(count($this->_allowedOrigins) == 0){
            return;
        }

        // Origin
        $originIsWildcard = in_array('*', $this->_allowedOrigins);
        $originValue = null;

        if($originIsWildcard){
            $originValue = '*';
        }
        else {

            $origin = $this->request->getHeader('Origin');
            $originDomain = $origin ? parse_url($origin, PHP_URL_HOST) : null;

            if($originDomain){

                $allowed = false;

                foreach($this->_allowedOrigins as $allowedOrigin){

                    // First try exact domain
                    if($originDomain == $allowedOrigin){

                        $allowed = true;
                        break;
                    }

                    // Parse wildcards
                    $expression = '/^' . str_replace('\*', '(.+)', preg_quote($allowedOrigin, '/')) . '$/';
                    if(preg_match($expression, $originDomain) == 1){

                        $allowed = true;
                        break;
                    }
                }

                if($allowed){

                    $originValue = $origin;
                }
            }
        }

        if($originValue != null){

            $this->response->setHeader('Access-Control-Allow-Origin', $originValue);

            // Allowed methods
            if(count($this->_allowedMethods) > 0){

                $this->response->setHeader('Access-Control-Allow-Methods', implode(',', $this->_allowedMethods));
            }

            // Allowed headers
            if(count($this->_allowedHeaders) > 0){

                $this->response->setHeader('Access-Control-Allow-Headers', implode(',', $this->_allowedHeaders));
            }
        }
    }
}
