<?php

namespace PhalconRest\Transformers\Documentation;

class ResourceTransformer extends \League\Fractal\TransformerAbstract
{
    public $defaultIncludes = [
        'endpoints'
    ];

    public function transform(\PhalconRest\Export\Documentation\Resource $resource)
    {
        return [
            'name' => $resource->getName(),
            'description' => $resource->getDescription(),
            'prefix' => $resource->getPath(),
            'fields' => $resource->getFields()
        ];
    }

    public function includeEndpoints(\PhalconRest\Export\Documentation\Resource $resource)
    {
        return $this->collection($resource->getEndpoints(), new EndpointTransformer);
    }
}