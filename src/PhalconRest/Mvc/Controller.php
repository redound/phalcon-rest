<?php

namespace PhalconRest\Mvc;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Constants\Services;

class Controller extends \Phalcon\Mvc\Controller
{
    public function onConstruct()
    {
        $this->fractal = $this->di->get(Services::FRACTAL_MANAGER);
    }

    public function respondWithArray($array, $key)
    {

        $response = [$key => $array];

        return $this->onResponse($response);
    }

    public function respondWithOK()
    {

        $response = ['result' => 'OK'];

        return $this->onResponse($response);
    }

    public function createItemWithOK($item, $callback, $resource_key)
    {

        $response = $this->createItem($item, $callback, $resource_key);
        $response['result'] = 'OK';

        return $this->onResponse($response);
    }

    public function createItem($item, $callback, $resource_key, $meta = [])
    {

        $resource = new Item($item, $callback, $resource_key);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $this->onResponse($response);
    }

    public function createCollection($collection, $callback, $resource_key, $meta = [])
    {

        $resource = new Collection($collection, $callback, $resource_key);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $this->onResponse($response);
    }

    public function onResponse($response) {

        return $response;
    }
}
