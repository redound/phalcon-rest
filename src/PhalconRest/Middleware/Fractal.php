<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use PhalconRest\Constants\Services;
use PhalconRest\Exceptions\Exception;

class Fractal extends \PhalconRest\Mvc\Plugin
{
    public $parseIncludes;

    public function __construct($parseIncludes=true)
    {
        $this->parseIncludes = $parseIncludes;
    }

    public function beforeExecuteRoute(Event $event, Micro $app)
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