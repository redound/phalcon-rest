<?php

namespace PhalconRest\Transformers\Documentation;

use PhalconRest\Export\Documentation\Endpoint as DocumentationEndpoint;
use PhalconRest\Transformers\Transformer;

class EndpointTransformer extends Transformer
{
    public function transform(DocumentationEndpoint $endpoint)
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
