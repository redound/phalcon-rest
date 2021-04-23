<?php

namespace PhalconRest\Acl;

interface MountingEnabledAdapterInterface extends \Phalcon\Acl\Adapter\AdapterInterface
{
    /**
     * Mounts the mountable object onto the ACL
     *
     * @param MountableInterface $mountable
     *
     * @return static
     */
    public function mount(MountableInterface $mountable);

    /**
     * Mounts an array of mountable objects onto the ACL
     *
     * @param MountableInterface[] $mountables
     *
     * @return static
     */
    public function mountMany(array $mountables);
}
