<?php

namespace PhalconRest\Middleware;

use \PhalconRest\Constants\Services as AppServices;

class UrlQuery extends \PhalconRest\Mvc\Plugin
{
    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Micro $app)
    {
        $params = $this->getDI()->get(AppServices::REQUEST)->getQuery();
        $query = $this->getDI()->get(AppServices::URL_QUERY_PARSER)->createQuery($params);

        $this->getDI()->get(AppServices::QUERY)->merge($query);
    }
}