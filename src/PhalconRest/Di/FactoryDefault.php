<?php

namespace PhalconRest\Di;

use PhalconRest\Acl\Adapter\Memory as Acl;
use PhalconRest\Auth\Manager as AuthManager;
use PhalconRest\Auth\TokenParsers\JWTTokenParser;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Constants\Services;
use PhalconRest\Data\Query;
use PhalconRest\Data\Query\QueryParsers\PhqlQueryParser;
use PhalconRest\Data\Query\QueryParsers\UrlQueryParser;
use PhalconRest\Exception;
use PhalconRest\Helpers\ErrorHelper;
use PhalconRest\Helpers\FormatHelper;
use PhalconRest\Http\Request;
use PhalconRest\Http\Response;
use PhalconRest\User\Service as UserService;

class FactoryDefault extends \Phalcon\Di\FactoryDefault
{
    public function __construct()
    {
        parent::__construct();

        $this->setShared(Services::REQUEST, new Request);
        $this->setShared(Services::RESPONSE, new Response);

        $this->setShared(Services::AUTH_MANAGER, new AuthManager);

        $this->setShared(Services::FRACTAL_MANAGER, function () {

            $className = '\League\Fractal\Manager';

            if (!class_exists($className)) {
                throw new Exception(ErrorCodes::GENERAL_SYSTEM, null,
                    '\League\Fractal\Manager was requested, but class could not be found');
            }

            return new $className();
        });

        $this->setShared(Services::USER_SERVICE, new UserService);

        $this->setShared(Services::TOKEN_PARSER, function () {

            return new JWTTokenParser('this_should_be_changed');
        });

        $this->setShared(Services::QUERY, new Query);

        $this->setShared(Services::PHQL_QUERY_PARSER, new PhqlQueryParser);

        $this->setShared(Services::URL_QUERY_PARSER, new UrlQueryParser);

        $this->setShared(Services::ACL, new Acl);

        $this->setShared(Services::ERROR_HELPER, new ErrorHelper);

        $this->setShared(Services::FORMAT_HELPER, new FormatHelper);
    }
}
