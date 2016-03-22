<?php

namespace PhalconRest\Acl\Adapter;

use PhalconRest\Acl\MountingEnabledAdapterInterface;

class Memory extends \Phalcon\Acl\Adapter\Memory implements MountingEnabledAdapterInterface
{
    use \AclAdapterMountTrait;
}
