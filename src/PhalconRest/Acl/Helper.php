<?php

namespace PhalconRest\Acl;

use Phalcon\Acl\Resource;
use PhalconRest\Acl;
use PhalconRest\Api;
use PhalconRest\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\Resource as ApiResource;

class Helper
{
    public function importManyApiResources(\Phalcon\Acl\Adapter $acl, array $apiResources)
    {
        /** @var ApiResource $apiResource */
        foreach ($apiResources as $apiResource) {
            $this->importApiResource($acl, $apiResource);
        }
    }

    public function importApiResource(\Phalcon\Acl\Adapter $acl, ApiResource $apiResource)
    {
        $apiEndpointIdentifiers = array_map(function(ApiEndpoint $apiEndpoint) {
            return $apiEndpoint->getIdentifier();
        }, $apiResource->getEndpoints());

        $acl->addResource(new Resource($apiResource->getPrefix(), $apiResource->getName()), $apiEndpointIdentifiers);

        $defaultAllowedRoles = $apiResource->getAllowedRoles();
        $defaultDeniedRoles = $apiResource->getDeniedRoles();

        foreach ($acl->getRoles() as $role) {

            /** @var ApiEndpoint $apiEndpoint */
            foreach ($apiResource->getEndpoints() as $apiEndpoint) {

                $rule = null;

                if (in_array($role, $defaultAllowedRoles)) {
                    $rule = true;
                }

                if (in_array($role, $defaultDeniedRoles)) {
                    $rule = false;
                }

                if (in_array($role, $apiEndpoint->getAllowedRoles())) {
                    $rule = true;
                }

                if (in_array($role, $apiEndpoint->getDeniedRoles())) {
                    $rule = false;
                }

                if ($rule === true) {
                    $acl->allow($role, $apiResource->getPrefix(), $apiEndpoint->getIdentifier());
                }

                if ($rule === false) {
                    $acl->deny($role, $apiResource->getPrefix(), $apiEndpoint->getIdentifier());
                }
            }
        }
    }
}