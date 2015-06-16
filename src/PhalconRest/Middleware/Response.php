<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;

class Response extends \Phalcon\Mvc\User\Plugin
{
    protected $response;

    public function __construct($responseservice)
    {
        $this->response = $this->di->get($responseservice);
    }

    public function afterHandleRoute(Event $event, Micro $app)
    {
        $this->response->send($app->getReturnedValue()); // Get return value from controller
        exit;
    }
}
