<?php

namespace PhalconRest\Mvc;

/**
 * PhalconRest\Mvc\Plugin
 * This class allows to access services in the services container by just only accessing a public property
 * with the same name of a registered service
 *
 * @property \PhalconRest\Http\Request $request;
 * @property \PhalconRest\Http\Response $response;
 * @property \PhalconRest\Auth\Manager $authManager
 * @property \PhalconRest\Auth\TokenParser $tokenParser
 */
class Plugin extends \Phalcon\Mvc\User\Plugin
{

}