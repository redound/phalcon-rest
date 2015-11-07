<?php

namespace PhalconRest\Documentation;

use League\Fractal;

class ResourceTransformer extends Fractal\TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'routes',
    ];

    /**
     * Turn this resource object into a generic array
     *
     * @return array
     */
    public function transform($resource)
    {
        return [
            'title' => $resource->resource,
        ];
    }

    public function includeRoutes($resource)
    {
        $endpoints = isset($resource->endpoints) ? $resource->endpoints : [];
        return $this->collection($endpoints, new EndpointTransformer, 'parent');
    }

}
