<?php

namespace PhalconRest\Mvc\Controllers;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Constants\Services;
use PhalconRest\Mvc\Controller;

class FractalController extends Controller
{
    /** @var \League\Fractal\Manager */
    protected $fractal;

    public function onConstruct()
    {
        $this->fractal = $this->di->get(Services::FRACTAL_MANAGER);
    }

    protected function getUser()
    {
        return $this->userService->getDetails();
    }

    protected function getUserId()
    {
        return (int)$this->userService->getIdentity();
    }

    protected function createArrayResponse($array, $key)
    {
        $response = [$key => $array];

        return $response;
    }

    protected function createOkResponse()
    {
        $response = ['result' => 'OK'];

        return $response;
    }

    protected function createItemOkResponse($item, $transformer, $resourceKey = null, $meta = [])
    {
        $response = ['result' => 'OK'];
        $response += $this->createItemResponse($item, $transformer, $resourceKey, $meta);

        return $response;
    }

    protected function createItemResponse($item, $transformer, $resourceKey = null, $meta = [])
    {
        $resource = new Item($item, $transformer, $resourceKey);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $response;
    }

    protected function createCollectionResponse($collection, $transformer, $resourceKey = null, $meta = [])
    {
        $resource = new Collection($collection, $transformer, $resourceKey);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $response;
    }
}
