<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use PhalconRest\Constants\HttpMethods;

class OptionsResponseMiddleware extends \PhalconRest\Mvc\Plugin
{
    public function beforeHandleRoute(Event $event, \PhalconRest\Api $api)
    {
        // OPTIONS request, just send the headers and respond OK
        if ($this->request->getMethod() == HttpMethods::OPTIONS) {

            $this->response->setJsonContent([
                'result' => 'OK',
            ]);

            return false;
        }
    }
}