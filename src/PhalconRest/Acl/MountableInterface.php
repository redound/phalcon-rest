<?php

namespace PhalconRest\Acl;

interface MountableInterface
{
    /**
     * Returns the ACL resources in the following format:
     *
     * [
     *   [ Resources, ['endpoint1', 'endpoint2'] ]
     * ]
     *
     * @return array
     */
    public function getAclResources();

    /**
     * Returns the ACL rules in the following format:
     *
     * [
     *   Acl::ALLOW => [['rolename', 'resourcename', 'endpointname], ['rolename', 'resourcename', 'endpointname]],
     *   Acl::DENY => [['rolename', 'resourcename', 'endpointname], ['rolename', 'resourcename', 'endpointname]]
     * ]
     *
     * @param array $roles The currently registered role on the ACL
     *
     * @return array
     */
    public function getAclRules(array $roles);
}
