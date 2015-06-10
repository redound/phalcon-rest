<?php

namespace PhalconRest\Middleware;

use PhalconRest\Constants\ErrorCodes as ErrorCodes;
use PhalconRest\Constants\Services;
use PhalconRest\Exceptions\UserException;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;

class Acl extends \Phalcon\Mvc\User\Plugin
{
    protected $authManager;
    protected $privateEndpoints;
    protected $publicEndpoints;

    public function __construct($privateEndpoints = [], $publicEndpoints = [])
    {
        $this->authManager = null;
        $this->privateEndpoints = $privateEndpoints;
        $this->publicEndpoints = $publicEndpoints;
    }

    protected function _getAcl()
    {
        /**
        TODO: Put this in cache
         **/
        if (is_null($this->persistent->acl) || true) {
            $acl = new \Phalcon\Acl\Adapter\Memory();
            $acl->setDefaultAction(\Phalcon\Acl::DENY);

            $acl->addRole(new \Phalcon\Acl\Role('Public'));
            $acl->addRole(new \Phalcon\Acl\Role('Private'));

            // Allow All Roles to access the Public resources
            foreach ($this->publicEndpoints as $endpoint) {
                $acl->addResource(new \Phalcon\Acl\Resource('api'), $endpoint);
                $acl->allow('Public', 'api', $endpoint);
                $acl->allow('Private', 'api', $endpoint);
            }

            foreach ($this->privateEndpoints as $endpoint) {
                $acl->addResource(new \Phalcon\Acl\Resource('api'), $endpoint);
                $acl->allow('Private', 'api', $endpoint);
            }

            $this->persistent->set('acl', $acl);

        }

        return $this->persistent->acl;
    }

    public function beforeExecuteRoute(Event $event, Micro $app)
    {
        $this->authManager = $this->di->get(Services::AUTH_MANAGER);
        $role = $this->authManager->loggedIn() ? 'Private' : 'Public';

        // Get the current resource/endpoint from the micro app
        $resource = 'api';
        $endpoint = $app->getRouter()->getMatchedRoute()->getPattern();

        // Get the access control list
        $acl = $this->_getAcl();

        // See if they have permission
        $allowed = $acl->isAllowed($role, $resource, $endpoint);

        if ($allowed != \Phalcon\Acl::ALLOW) {
            if ($this->authManager->loggedIn()) {

                throw new UserException(ErrorCodes::AUTH_FORBIDDEN);

            } else {

                throw new UserException(ErrorCodes::AUTH_UNAUTHORIZED);

            }

            // Path not allowed!
            return false;
        }
    }
}
