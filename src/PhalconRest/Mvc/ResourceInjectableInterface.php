<?php

namespace PhalconRest\Mvc;

interface ResourceInjectableInterface
{
    public function setResource(\PhalconRest\Api\Resource $resource);
}