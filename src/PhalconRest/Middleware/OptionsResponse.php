<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;

class OptionsResponse extends \PhalconRest\Mvc\Plugin
{
    public function beforeHandleRoute(Event $event, Micro $app)
    {
        // OPTIONS have no body, send the headers, exit
        if ($this->request->getMethod() == 'OPTIONS') {

            $this->response->setJsonContent([
                'result' => 'OK',
            ]);

            return false;
        }
    }
}