<?php

use Phalcon\Acl\Enum;

trait AclAdapterMountTrait
{
    public function mountMany(array $mountables)
    {
        foreach ($mountables as $mountable) {
            $this->mount($mountable);
        }

        return $this;
    }

    public function mount(\PhalconRest\Acl\MountableInterface $mountable)
    {
        if ($this instanceof \Phalcon\Acl\Adapter\AdapterInterface) {

            $resources = $mountable->getAclResources();
            $rules = $mountable->getAclRules($this->getRoles());

            // Mount resources
            foreach ($resources as $resourceConfig) {

                if (count($resourceConfig) == 0) {
                    continue;
                }

                $this->addComponent($resourceConfig[0], count($resourceConfig) > 1 ? $resourceConfig[1] : null);
            }

            // Mount rules
            $allowedRules = array_key_exists(Enum::ALLOW, $rules) ? $rules[Enum::ALLOW] : [];
            $deniedRules = array_key_exists(Enum::DENY, $rules) ? $rules[Enum::DENY] : [];

            foreach ($allowedRules as $ruleConfig) {

                if (count($ruleConfig) < 2) {
                    continue;
                }

                $this->allow($ruleConfig[0], $ruleConfig[1], count($ruleConfig) > 2 ? $ruleConfig[2] : null);
            }

            foreach ($deniedRules as $ruleConfig) {

                if (count($ruleConfig) < 2) {
                    continue;
                }

                $this->deny($ruleConfig[0], $ruleConfig[1], count($ruleConfig) > 2 ? $ruleConfig[2] : null);
            }
        }
    }
}
