<?php

namespace PhalconRest\Documentation;

use League\Fractal;

class PostmanCollectionTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'requests',
    ];

    /**
     * Turn this resource object into a generic array
     *
     * @return array
     */
    public function transform($collection)
    {
        return [
            'id' => $collection->id,
            'name' => $collection->name,
        ];
    }

    public function includeRequests($collection)
    {
        return $this->collection($collection->requests, new PostmanRequestTransformer, 'parent');
    }
}
