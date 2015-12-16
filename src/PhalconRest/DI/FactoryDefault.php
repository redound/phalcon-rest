<?php

namespace PhalconRest\DI;

use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Constants\Services;

class FactoryDefault extends \Phalcon\Di\FactoryDefault
{
    public function __construct()
    {
        parent::__construct();

        $this->setShared(Services::REQUEST, new \PhalconRest\Http\Request());
        $this->setShared(Services::RESPONSE, new \PhalconRest\Http\Response());

        $this->setShared(Services::AUTH_MANAGER, new \PhalconRest\Auth\Manager());

        $this->setShared(Services::FRACTAL_MANAGER, function () {

            $className = '\League\Fractal\Manager';

            if(!class_exists($className)){
                throw new \Exception(ErrorCodes::GEN_SYSTEM, '\League\Fractal\Manager was requested, but class could not be found');
            }

            return new $className();
        });

        $this->setShared(Services::TOKEN_PARSER, function () {

            return new \PhalconRest\Auth\TokenParser\JWT('this_should_be_changed');
        });

        $this->setShared(Services::API_SERVICE, function(){

            return new \PhalconRest\Api\Service();
        });

        $this->setShared(Services::QUERY, function(){

            return new \PhalconRest\Data\Query\Query();
        });

        $this->setShared(Services::PHQL_QUERY_PARSER, function(){

            return new \PhalconRest\Data\Query\Parser\Phql();
        });

        $this->setShared(Services::URL_QUERY_PARSER, function(){

            return new \PhalconRest\Data\Query\Parser\Url();
        });
    }
}