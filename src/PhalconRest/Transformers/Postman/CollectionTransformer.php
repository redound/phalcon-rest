<?php

namespace PhalconRest\Transformers\Postman;

use League\Fractal;

class CollectionTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'requests',
    ];

    public function transform(\PhalconRest\Export\Postman\Collection $collection)
    {
        return [
            'id' => $collection->id,
            'name' => $collection->name,
        ];
    }

    public function includeRequests(\PhalconRest\Export\Postman\Collection $collection)
    {
        return $this->collection($collection->getRequests(), new RequestTransformer, 'parent');
    }
}
