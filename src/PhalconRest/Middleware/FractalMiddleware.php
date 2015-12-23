<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use PhalconRest\Constants\Services;

class FractalMiddleware extends \PhalconRest\Mvc\Plugin
{
    public $parseIncludes;

    public function __construct($parseIncludes=true)
    {
        $this->parseIncludes = $parseIncludes;
    }

    public function beforeExecuteRoute(Event $event, \PhalconRest\Api $api)
    {
        /** @var \League\Fractal\Manager $fractal */
        $fractal = $this->di->get(Services::FRACTAL_MANAGER);

        if($this->parseIncludes){

            $include = $this->request->getQuery('include');

            if(!is_null($include)){
                $fractal->parseIncludes($include);
            }
        }
    }
}