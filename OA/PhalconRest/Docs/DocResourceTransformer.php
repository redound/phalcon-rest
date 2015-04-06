<?php

namespace OA\PhalconRest\Docs;

use League\Fractal;

class DocResourceTransformer extends Fractal\TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
	protected $defaultIncludes = [
		'routes'
	];

	/**
	 * Turn this resource object into a generic array
	 *
	 * @return array
	 */
	public function transform($resource)
	{

		return [
			'title' => $resource->resource
		];
	}

	public function includeRoutes($resource)
	{

		$routes = isset($resource->routes) ? $resource->routes : [];
		return $this->collection($routes, new DocRouteTransformer, 'parent');
	}

}
