<?php

namespace PhalconRest\Middleware;

use PhalconRest\Constants\Services;

class UrlQueryMiddleware extends \PhalconRest\Mvc\Plugin
{
    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \PhalconRest\Api $api)
    {
        $params = $this->getDI()->get(Services::REQUEST)->getQuery();
        $query = $this->getDI()->get(Services::URL_QUERY_PARSER)->createQuery($params);

        $this->getDI()->get(Services::QUERY)->merge($query);
    }
}