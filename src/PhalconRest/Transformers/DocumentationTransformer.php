<?php

namespace PhalconRest\Transformers;

use PhalconRest\Export\Documentation;

class DocumentationTransformer extends \League\Fractal\TransformerAbstract
{
    public $defaultIncludes = [
        'routes',
        'resources'
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
        return $this->collection($documentation->getRoutes(), new \PhalconRest\Transformers\Documentation\RouteTransformer, 'parent');
    }

    public function includeResources(Documentation $documentation)
    {
        return $this->collection($documentation->getResources(), new \PhalconRest\Transformers\Documentation\ResourceTransformer, 'parent');
    }
}