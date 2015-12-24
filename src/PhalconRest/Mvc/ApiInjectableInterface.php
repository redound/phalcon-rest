<?php

namespace PhalconRest\Mvc;

interface ApiInjectableInterface
{
    public function setApi(\PhalconRest\Api $api);
}