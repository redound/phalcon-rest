<?php
/**
 * Created by PhpStorm.
 * User: bartblok
 * Date: 05-11-15
 * Time: 15:48
 */

namespace PhalconRest\Mvc;

/**
 * PhalconRest\Mvc\Plugin
 * This class allows to access services in the services container by just only accessing a public property
 * with the same name of a registered service
 *
 * @property \PhalconRest\Http\Request $request;
 * @property \PhalconRest\Http\Response $response;
 * @property \PhalconRest\Auth\Manager $authManager
 */
class Plugin extends \Phalcon\Mvc\User\Plugin
{

}