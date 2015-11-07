<?php

namespace PhalconRest\Middleware;

use PhalconRest\Constants\ErrorCodes as ErrorCodes;
use PhalconRest\Exceptions\UserException;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;

class Acl extends \PhalconRest\Mvc\Plugin
{
    const ROLE_PUBLIC = 'Public';
    const ROLE_PRIVATE = 'Private';

    const RESOURCE_API = 'api';

    protected $acl;

    protected $privateEndpoints;
    protected $publicEndpoints;


    public function __construct($privateEndpoints = [], $publicEndpoints = [])
    {
        $this->acl = null;
        $this->privateEndpoints = $privateEndpoints;
        $this->publicEndpoints = $publicEndpoints;
    }

    protected function _getAcl()
    {
        if(!$this->acl) {

            $acl = new \Phalcon\Acl\Adapter\Memory();
            $acl->setDefaultAction(\Phalcon\Acl::DENY);

            $acl->addRole(new \Phalcon\Acl\Role(self::ROLE_PUBLIC));
            $acl->addRole(new \Phalcon\Acl\Role(self::ROLE_PRIVATE));

            // Allow All Roles to access the Public resources
            foreach ($this->publicEndpoints as $endpoint) {

                $acl->addResource(new \Phalcon\Acl\Resource(self::RESOURCE_API), $endpoint);
                $acl->allow(self::ROLE_PUBLIC, self::RESOURCE_API, $endpoint);
                $acl->allow(self::ROLE_PRIVATE, self::RESOURCE_API, $endpoint);
            }

            foreach ($this->privateEndpoints as $endpoint) {

                $acl->addResource(new \Phalcon\Acl\Resource(self::RESOURCE_API), $endpoint);
                $acl->allow(self::ROLE_PRIVATE, self::RESOURCE_API, $endpoint);
            }

            $this->acl = $acl;
        }

        return $this->acl;
    }

    public function beforeExecuteRoute(Event $event, Micro $app)
    {
        $role = $this->authManager->loggedIn() ? self::ROLE_PRIVATE : self::ROLE_PUBLIC;

        // Get the current resource/endpoint from the micro app
        $endpoint = $app->getRouter()->getMatchedRoute()->getPattern();

        // Get the access control list
        $acl = $this->_getAcl();

        // See if they have permission
        $allowed = $acl->isAllowed($role, self::RESOURCE_API, $endpoint);

        if ($allowed != \Phalcon\Acl::ALLOW) {

            if ($this->authManager->loggedIn()) {

                throw new UserException(ErrorCodes::AUTH_FORBIDDEN);

            } else {

                throw new UserException(ErrorCodes::AUTH_UNAUTHORIZED);
            }
        }
    }
}
