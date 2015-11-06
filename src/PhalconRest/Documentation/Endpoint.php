<?php

namespace PhalconRest\Documentation;

class Endpoint
{
    public function __construct($resource)
    {
        $this->resource = $resource;
        $this->title = null;
        $this->method = null;
        $this->route = null;
        $this->parameters = null;
        $this->description = null;
        $this->includeTypes = null;
        $this->headers = null;
        $this->response = null;
        $this->responseExample = null;
        $this->requestExample = null;
    }
}
