<?php

namespace PhalconRest\Mvc;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Constants\Services;
use PhalconRest\Exceptions\Exception;

class FractalController extends Controller
{
    /** @var \League\Fractal\Manager */
    protected $fractal;

    public function onConstruct()
    {
        $this->fractal = $this->di->get(Services::FRACTAL_MANAGER);
    }

    public function respondArray($array, $key)
    {
        $response = [$key => $array];

        return $this->respond($response);
    }

    public function respondOK()
    {
        $response = ['result' => 'OK'];

        return $this->respond($response);
    }

    public function responseItemOK($item, $callback, $resource_key)
    {
        $response = $this->respondItem($item, $callback, $resource_key);
        $response['result'] = 'OK';

        return $this->respond($response);
    }

    public function respondItem($item, $callback, $resource_key, $meta = [])
    {
        $resource = new Item($item, $callback, $resource_key);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $this->respond($response);
    }

    public function respondCollection($collection, $callback, $resource_key, $meta = [])
    {
        $resource = new Collection($collection, $callback, $resource_key);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $this->respond($response);
    }

    public function respond($response)
    {
        return $response;
    }
}
