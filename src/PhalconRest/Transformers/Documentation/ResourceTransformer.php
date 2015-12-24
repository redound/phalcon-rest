<?php

namespace PhalconRest\Transformers\Documentation;

class ResourceTransformer extends \League\Fractal\TransformerAbstract
{
    public $defaultIncludes = [
        'endpoints'
    ];

    public function transform(\PhalconRest\Export\Documentation\Resource $resource)
    {
        /** @var \PhalconRest\Api\Resource $details */
        $details = $resource->getDetails();

        return [
            'name' => $details->getName(),
            'description' => $details->getDescription(),
            'prefix' => $details->getPrefix(),
            'allowedRoles' => $details->getAllowedRoles(),
            'deniedRoles' => $details->getDeniedRoles(),
            'model' => $details->getModel(),
            'source' => $resource->getSource(),
            'columnMap' => $resource->getColumnMap(),
            'whitelist' => $resource->getWhitelist(),
            'dataTypes' => $resource->getDataTypes(),
            'transformer' => $details->getTransformer()
        ];
    }

    public function includeEndpoints(\PhalconRest\Export\Documentation\Resource $resource)
    {
        return $this->collection($resource->getEndpoints(), new EndpointTransformer, 'parent');
    }
}