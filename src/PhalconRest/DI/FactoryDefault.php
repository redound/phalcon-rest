<?php

namespace PhalconRest\DI;

use PhalconRest\Constant\ErrorCode;
use PhalconRest\Constant\Service;

class FactoryDefault extends \Phalcon\Di\FactoryDefault
{
    public function __construct()
    {
        parent::__construct();

        $this->setShared(Service::REQUEST, new \PhalconRest\Http\Request());
        $this->setShared(Service::RESPONSE, new \PhalconRest\Http\Response());

        $this->setShared(Service::AUTH_MANAGER, new \PhalconRest\Auth\Manager());
        $this->setShared(Service::ACL_SERVICE, new \PhalconRest\Acl\Service());

        $this->setShared(Service::FRACTAL_MANAGER, function () {

            $className = '\League\Fractal\Manager';

            if(!class_exists($className)){
                throw new \Exception(ErrorCode::GEN_SYSTEM, '\League\Fractal\Manager was requested, but class could not be found');
            }

            return new $className();
        });

        $this->setShared(Service::TOKEN_PARSER, function () {

            return new \PhalconRest\Auth\TokenParser\JWT('this_should_be_changed');
        });

        $this->setShared(Service::QUERY, function(){

            return new \PhalconRest\Data\Query();
        });

        $this->setShared(Service::PHQL_QUERY_PARSER, function(){

            return new \PhalconRest\Data\Query\Parser\Phql();
        });

        $this->setShared(Service::URL_QUERY_PARSER, function(){

            return new \PhalconRest\Data\Query\Parser\Url();
        });
    }
}