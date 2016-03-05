<?php

namespace PhalconRest\Mvc;

/**
 * PhalconRest\Mvc\Plugin
 * This class allows to access services in the services container by just only accessing a public property
 * with the same name of a registered service
 *
 * @property \PhalconRest\Api $application
 * @property \PhalconRest\Http\Request $request
 * @property \PhalconRest\Http\Response $response
 * @property \Phalcon\Acl\AdapterInterface $acl
 * @property \PhalconRest\Auth\Manager $authManager
 * @property \PhalconRest\User\Service $userService
 * @property \PhalconRest\Helpers\ErrorHelper $errorHelper
 * @property \PhalconRest\Helpers\FormatHelper $formatHelper
 * @property \PhalconRest\Auth\TokenParserInterface $tokenParser
 * @property \PhalconRest\Data\Query $query
 * @property \PhalconRest\Data\Query\QueryParsers\PhqlQueryParser $phqlQueryParser
 * @property \PhalconRest\Data\Query\QueryParsers\UrlQueryParser $urlQueryParser
 */

class Plugin extends \Phalcon\Mvc\User\Plugin
{

}