<?php

namespace PhalconRest\Middleware;

use \PhalconRest\Constants\Services as AppServices;

class Queries extends \PhalconRest\Mvc\Plugin
{
    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Micro $app)
    {
        $params = $this->get(AppServices::REQUEST)->getQuery();
        $query = $this->get(AppServices::URL_QUERY_PARSER)->createQuery($params);
        $this->get(AppServices::QUERY)->merge($query);
    }
}