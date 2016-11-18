<?php

namespace PhalconRest\Transformers\Postman;

use PhalconRest\Export\Postman\Request as PostmanRequest;
use PhalconRest\Transformers\Transformer;

class RequestTransformer extends Transformer
{
    public function transform(PostmanRequest $request)
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
