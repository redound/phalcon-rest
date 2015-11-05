<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;

class JsonResponse extends \PhalconRest\Mvc\Plugin
{
    public function afterHandleRoute(Event $event, Micro $app)
    {
        $this->response->setJsonContent($app->getReturnedValue());

        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET,HEAD,PUT,PATCH,POST,DELETE');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
        $this->response->setHeader('E-Tag', md5($this->response->getContent()));
        $this->response->setContentType('application/json');

        $this->response->send();
    }
}