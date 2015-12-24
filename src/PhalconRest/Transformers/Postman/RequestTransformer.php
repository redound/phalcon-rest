<?php

namespace PhalconRest\Transformers\Postman;

use League\Fractal;

class RequestTransformer extends Fractal\TransformerAbstract
{
    /**
     * Turn this resource object into a generic array
     *
     * @return array
     */
    public function transform(\PhalconRest\Export\Postman\Request $request)
    {
        return [
            'collectionId' => $request->collectionId,
            'id' => $request->id,
            'name' => $request->name,
            'description' => $request->description,
            'url' => $request->url,
            'method' => $request->method,
            'headers' => $request->headers,
            'data' => $request->data,
            'dataMode' => $request->dataMode,
        ];
    }
}
