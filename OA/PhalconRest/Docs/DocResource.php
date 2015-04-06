<?php

namespace OA\PhalconRest\Docs;

class DocResource {

	protected $_methods;
	protected $_collection;
	public $routes = [];

	public function __construct($resource, $collection, $annotations){

		$this->resource = $resource;
		$this->_collection = $collection;
		$this->_parseHandlers($collection->getHandlers());
		$this->_parseAnnotations($annotations);
	}

	protected function getMethod($method){

		return isset($this->methods[$method]) ? $this->methods[$method] : false;
	}

	protected function parseHandlers($handlers){

		foreach($handlers as $handler){

			$method = $handler[2];
			$this->methods[$method] = $handler;
		}
	}

	protected function parseAnnotations($methods){

		if (empty($methods)) return;

		foreach($methods as $method => $annotations){

			$docRoute = new DocRoute($this->resource);

			if ($this->getMethod($method)){

				$methodData = $this->getMethod($method);
				$docRoute->method = $methodData[0];
				$docRoute->route = $this->collection->getPrefix() . $methodData[1];
			}

			foreach($annotations as $description){

				switch($description->getName()){

					case 'DocTitle':
						$docRoute->title = $description->getArgument(0);
						break;
					case 'DocResource':
						$docRoute->resource = $description->getArgument(0);
						break;
					case 'DocDescription':
						$docRoute->description = $description->getArgument(0);
						break;
					case 'DocResponse':
						$docRoute->response = $description->getArgument(0);
						break;
					case 'DocResponseExample':
						$docRoute->responseExample = $description->getArgument(0);
						break;
					case 'DocRequestExample':
						$docRoute->requestExample = $description->getArgument(0);
						break;
					case 'DocIncludeTypes':
						$docRoute->includeTypes = $description->getArgument(0);
						break;
					case 'DocParameters':
						$docRoute->parameters = $description->getArgument(0);
						break;
					case 'DocHeaders':
						$docRoute->headers = $description->getArgument(0);
						break;
				}
			}

			$this->routes[] = $docRoute;
		}
	}
}
