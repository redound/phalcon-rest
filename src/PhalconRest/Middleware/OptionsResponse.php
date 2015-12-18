<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;

class OptionsResponse extends \PhalconRest\Mvc\Plugin
{
    public function beforeHandleRoute(Event $event, \PhalconRest\Api $api)
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