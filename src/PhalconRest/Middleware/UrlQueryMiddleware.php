<?php

namespace PhalconRest\Middleware;

use \PhalconRest\Constant\Service as Service;

class UrlQueryMiddleware extends \PhalconRest\Mvc\Plugin
{
    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \PhalconRest\Api $api)
    {
        $params = $this->getDI()->get(Service::REQUEST)->getQuery();
        $query = $this->getDI()->get(Service::URL_QUERY_PARSER)->createQuery($params);

        $this->getDI()->get(Service::QUERY)->merge($query);
    }
}