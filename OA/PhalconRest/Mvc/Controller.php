<?php

use League\Fractal\Resource\Collection,
	League\Fractal\Resource\Item;

class Controller extends \Phalcon\Mvc\Controller
{

	public function respondWithArray($array, $key)
	{

		return [$key=>$array];
	}

	public function respondWithOK()
	{

		return ['result'=>'OK'];
	}

	public function createItemWithOK($item, $callback, $resource_key)
	{

		$response = $this->createItem($item, $callback, $resource_key);
		$response['result'] = 'OK';

		return $response;
	}

	public function createItem($item, $callback, $resource_key)
	{

		$resource = new Item($item, $callback, $resource_key);
		return $this->fractal->createData($resource)->toArray();
	}

	public function createCollection($collection, $callback, $resource_key)
	{

		$resource = new Collection($collection, $callback, $resource_key);
		return $this->fractal->createData($resource)->toArray();
	}
}
