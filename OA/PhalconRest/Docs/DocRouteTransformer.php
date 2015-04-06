<?php

namespace OA\PhalconRest\Docs;

use League\Fractal;

class DocRouteTransformer extends Fractal\TransformerAbstract
{
	/**
	 * Turn this resource object into a generic array
	 *
	 * @return array
	 */
	public function transform($route)
	{
		
		return [
			'resource' => $route->resource,
			'method' => $route->method,
			'route' => $route->route,
			'description' => $route->description,
			'title' => $route->title,
			'parameters' => $route->parameters,
			'headers' => $route->headers,
			'response' => $route->response,
			'responseExample' => $route->responseExample,
			'requestExample' => $route->requestExample,
			'includeTypes' => $route->includeTypes,
		];
	}
}
