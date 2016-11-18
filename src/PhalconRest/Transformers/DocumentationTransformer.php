<?php

namespace PhalconRest\Transformers;

use PhalconRest\Export\Documentation;
use PhalconRest\Transformers\Documentation\ApiCollectionTransformer;
use PhalconRest\Transformers\Documentation\RouteTransformer;

class DocumentationTransformer extends Transformer
{
    public $defaultIncludes = [
        'routes',
        'collections'
    ];

    public function transform(Documentation $documentation)
    {
        return [
            'name' => $documentation->name,
            'basePath' => $documentation->basePath
        ];
    }

    public function includeRoutes(Documentation $documentation)
    {
        return $this->collection($documentation->getRoutes(), new RouteTransformer);
    }

    public function includeCollections(Documentation $documentation)
    {
        return $this->collection($documentation->getCollections(), new ApiCollectionTransformer);
    }
}
