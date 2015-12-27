<?php

namespace PhalconRest\Transformers\Documentation;

use Phalcon\Mvc\Router\Route;

class RouteTransformer extends \League\Fractal\TransformerAbstract
{
    public function transform(Route $route)
    {
        return [
            'name' => $route->getName(),
            'pattern' => $route->getPattern()
        ];
    }
}