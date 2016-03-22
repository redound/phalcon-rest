<?php

namespace PhalconRest\Transformers\Documentation;

use PhalconRest\Export\Documentation\Collection as DocumentationCollection;
use PhalconRest\Transformers\Transformer;

class CollectionTransformer extends Transformer
{
    public $defaultIncludes = [
        'endpoints'
    ];

    public function transform(DocumentationCollection $collection)
    {
        return [
            'name' => $collection->getName(),
            'description' => $collection->getDescription(),
            'prefix' => $collection->getPath(),
            'fields' => $collection->getFields()
        ];
    }

    public function includeEndpoints(DocumentationCollection $collection)
    {
        return $this->collection($collection->getEndpoints(), new EndpointTransformer);
    }
}
