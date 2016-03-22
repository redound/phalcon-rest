<?php

namespace PhalconRest\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconRest\Constants\HttpMethods;
use PhalconRest\Mvc\Plugin;

class OptionsResponseMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeHandleRoute()
    {
        // OPTIONS request, just send the headers and respond OK
        if ($this->request->getMethod() == HttpMethods::OPTIONS) {

            $this->response->setJsonContent([
                'result' => 'OK',
            ]);

            return false;
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}