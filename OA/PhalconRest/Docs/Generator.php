<?php

namespace OA\PhalconRest\Docs;

use League\Fractal\Resource\Collection,
	League\Fractal\Resource\Item;

class Generator extends \Phalcon\Mvc\User\Plugin {

	protected $_reader;
	protected $_collectionClasses = [];
	protected $_handlers = [];

	public function __construct()
	{

		$this->_reader = new \Phalcon\Annotations\Adapter\Memory();
	}

	protected function getCollectionClasses()
	{

		if (empty($this->collectionClasses)) {

			foreach ($this->config->collections as $collection){

				$this->collectionClasses[] = new $collection;
			}
		}

		return $this->collectionClasses;
	}

	protected function getAnnotationsFromCollection($collection)
	{

		$handler = $collection->getHandler();
		$reflector = $this->_reader->get($handler);
		return [
			"class" => $reflector->getClassAnnotations(),
			"methods" => $reflector->getMethodsAnnotations()
		];
	}

	protected function getAnnotationsFromCollections()
	{

		$data = [];

		foreach ($this->getCollectionClasses() as $collection){

			$handler = $collection->getHandler();
			$annotations = $this->getAnnotationsFromCollection($collection);
			$classAnnotations = $annotations["class"];

			$resource = "Untitled Resource - Specify @resource above class";

			if (is_object($classAnnotations) && $classAnnotations->has("resource")){

				$resource = $classAnnotations->get("resource")->getArgument(0);
			}

			$data[$handler]["resource"] = $resource;
			$data[$handler]["collection"] = $collection;
			$data[$handler]["annotations"] = $annotations["methods"];
		}

		return $data;
	}

	protected function getDocumentedRoutesFromCollections()
	{

		$collections = $this->getAnnotationsFromCollections();

		foreach($collections as $collection){

			$data[] = new DocResource($collection["resource"], $collection["collection"], $collection["annotations"]);
		}

		return $data;
	}

	public function generate()
	{

		$resources = $this->getDocumentedRoutesFromCollections();
		$resource = new Collection($resources, new DocResourceTransformer, 'resources');

		return $this->fractal->createData($resource)->toArray();
	}
}
