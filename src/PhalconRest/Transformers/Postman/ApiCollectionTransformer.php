<?php

namespace PhalconRest\Transformers\Postman;

use PhalconRest\Export\Postman\ApiCollection as PostmanCollection;
use PhalconRest\Transformers\Transformer;

class ApiCollectionTransformer extends Transformer
{
    protected $defaultIncludes = [
        'requests',
    ];

    public function transform(PostmanCollection $collection)
    {
        return [
            'id' => $collection->id,
            'name' => $collection->name,
        ];
    }

    public function includeRequests(PostmanCollection $collection)
    {
        return $this->collection($collection->getRequests(), new RequestTransformer);
    }
}
