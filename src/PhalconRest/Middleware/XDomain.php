<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;

class XDomain extends \Phalcon\Mvc\User\Plugin
{
    protected $view;

    public function __construct($viewservice)
    {
        $this->view = $this->di->get($viewservice);
    }

    public function setRoute($route = '/proxy.html')
    {
        $this->route = $route;
    }

    public function setViewPath($viewPath = '')
    {
        $this->viewPath = $viewPath;
    }

    public function setHostName($hostName = '')
    {
        $this->hostName = $hostName;
    }

    public function beforeHandleRoute(Event $event, Micro $app)
    {
        $app->get($this->route, function () use ($app) {

            echo $this->view->render($this->viewPath, ['client' => $this->hostName]);
            exit;
        });
    }
}
