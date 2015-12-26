<?php

namespace PhalconRest\Transformers\Documentation;

use PhalconRest\Api\Endpoint;

class EndpointTransformer extends \League\Fractal\TransformerAbstract
{
    public function transform(\PhalconRest\Export\Documentation\Endpoint $endpoint)
    {
        return [
            'name' => $endpoint->getName(),
            'description' => $endpoint->getDescription(),
            'httpMethod' => $endpoint->getHttpMethod(),
            'path' => $endpoint->getPath(),
            'exampleResponse' => $endpoint->getExampleResponse(),
            'allowedRoles' => $endpoint->getAllowedRoles()
        ];
    }
}