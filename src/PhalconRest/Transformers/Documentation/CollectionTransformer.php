<?php

namespace PhalconRest\Transformers\Documentation;

use PhalconRest\Transformers\Transformer;

class CollectionTransformer extends Transformer
{
    public $defaultIncludes = [
        'endpoints'
    ];

    public function transform(\PhalconRest\Export\Documentation\Collection $collection)
    {
        return [
            'name' => $collection->getName(),
            'description' => $collection->getDescription(),
            'prefix' => $collection->getPath(),
            'fields' => $collection->getFields()
        ];
    }

    public function includeEndpoints(\PhalconRest\Export\Documentation\Collection $collection)
    {
        return $this->collection($collection->getEndpoints(), new EndpointTransformer);
    }
}