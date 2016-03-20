<?php

namespace PhalconRest\Transformers;

use PhalconRest\Export\Documentation;

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
        return $this->collection($documentation->getRoutes(), new \PhalconRest\Transformers\Documentation\RouteTransformer);
    }

    public function includeCollections(Documentation $documentation)
    {
        return $this->collection($documentation->getCollections(), new \PhalconRest\Transformers\Documentation\CollectionTransformer);
    }
}