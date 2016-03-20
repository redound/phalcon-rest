<?php

namespace PhalconRest\Transformers\Documentation;

use PhalconRest\Transformers\Transformer;

class EndpointTransformer extends Transformer
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