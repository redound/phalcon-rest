<?php

namespace PhalconRest\Transformers;

use Phalcon\Di;
use PhalconRest\Exception;

class Transformer extends \League\Fractal\TransformerAbstract
{
    /**
     * Dependency Injector
     *
     * @var \Phalcon\DiInterface
     */
    protected $_dependencyInjector;

    public function setDI(\Phalcon\DiInterface $dependencyInjector)
	{
		$this->_dependencyInjector = $dependencyInjector;
	}

    public function getDI()
	{
        $dependencyInjector = $this->_dependencyInjector;

		if(!$dependencyInjector) {
            $dependencyInjector = Di::getDefault();
		}

		return $dependencyInjector;
	}

	public function __get($propertyName)
	{
        $dependencyInjector = null;
        $service = null;
        $persistent = null;

        $dependencyInjector = $this->_dependencyInjector;

        if(!$dependencyInjector) {
            $dependencyInjector = Di::getDefault();
        }

        if(!$dependencyInjector) {
            throw new Exception("A dependency injection object is required to access the application services");
        }

		/**
         * Fallback to the PHP userland if the cache is not available
         */
		if($dependencyInjector->has($propertyName)) {

            $service = $dependencyInjector->getShared($propertyName);
			$this->{$propertyName} = $service;

			return $service;
		}

		if ($propertyName == "di") {

            $this->{"di"} = $dependencyInjector;
			return $dependencyInjector;
		}

		/**
         * A notice is shown if the property is not defined and isn't a valid service
         */
		trigger_error("Access to undefined property " . $propertyName);
		return null;
	}


}