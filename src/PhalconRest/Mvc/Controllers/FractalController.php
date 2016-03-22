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

        return $this->createResponse($response);
    }

    protected function createResponse($response)
    {
        return $response;
    }

    protected function createOkResponse()
    {
        $response = ['result' => 'OK'];

        return $this->createResponse($response);
    }

    protected function createItemOkResponse($item, $transformer, $resourceKey = null, $meta = null)
    {
        $response = ['result' => 'OK'];
        $response += $this->createItemResponse($item, $transformer, $resourceKey, $meta);

        return $this->createResponse($response);
    }

    protected function createItemResponse($item, $transformer, $resourceKey = null, $meta = null)
    {
        $resource = new Item($item, $transformer, $resourceKey);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta ? $meta : []);

        return $this->createResponse($response);
    }

    protected function createCollectionResponse($collection, $transformer, $resourceKey = null, $meta = null)
    {
        $resource = new Collection($collection, $transformer, $resourceKey);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta ? $meta : []);

        return $this->createResponse($response);
    }
}
