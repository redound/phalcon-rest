<?php

namespace PhalconRest\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconRest\Constants\Services;
use PhalconRest\Mvc\Plugin;

class UrlQueryMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeExecuteRoute()
    {
        $params = $this->getDI()->get(Services::REQUEST)->getQuery();
        $query = $this->getDI()->get(Services::URL_QUERY_PARSER)->createQuery($params);

        $this->getDI()->get(Services::QUERY)->merge($query);
    }

    public function call(Micro $api)
    {
        return true;
    }
}