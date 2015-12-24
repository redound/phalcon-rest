<?php

namespace PhalconRest\Transformers\Documentation;

use PhalconRest\Api\Endpoint;

class EndpointTransformer
{
    public function transform(\PhalconRest\Export\Documentation\Endpoint $endpoint)
    {
        /** @var Endpoint $details */
        $details = $endpoint->getDetails();

        return [
            'name' => $details->getName(),
            'description' => $details->getDescription(),
            'httpMethod' => $details->getHttpMethod(),
            'path' => $details->getPath(),
            'allowedRoles' => $endpoint->getAllowedRoles(),
            'deniedRoles' => $endpoint->getDeniedRoles()
        ];
    }
}