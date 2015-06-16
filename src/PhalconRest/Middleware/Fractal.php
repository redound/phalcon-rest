<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;

class Fractal extends \Phalcon\Mvc\User\Plugin
{
    protected $fractal;
    protected $request;

    public function __construct($fractalservice, $requestservice)
    {
        $this->fractal = $this->di->get($fractalservice);
        $this->request = $this->di->get($requestservice);
    }

    public function beforeExecuteRoute(Event $event, Micro $app)
    {
        $include = $this->request->getQuery('include');

        if (!is_null($include)) {
            $this->fractal->parseIncludes($include);
        }
    }
}
