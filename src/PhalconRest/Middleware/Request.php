<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;

class Request extends \Phalcon\Mvc\User\Plugin
{
    protected $request;
    protected $response;

    public function __construct($requestservice, $responseservice)
    {
        $this->request = $this->di->get($requestservice);
        $this->response = $this->di->get($responseservice);
    }

    public function beforeExecuteRoute(Event $event, Micro $app)
    {
        // OPTIONS have no body, send the headers, exit
        if ($this->request->getMethod() == 'OPTIONS') {

            $this->response->send([
                'result' => 'OK',
            ]);
            exit;
        }
    }
}
