<?php

namespace OA\PhalconRest\DI;

class PhalconRestDI extends \Phalcon\DI\FactoryDefault
{

	public function __construct()
	{
		// Set up default services
		parent::__construct();

		$di = $this;

		$di->set('fractal', function(){

			$fractal = new \League\Fractal\Manager;
			$fractal->setSerializer(new \OA\Fractal\CustomSerializer);
			return $fractal;
		});

		// Prepare the request object
		$di->setShared('request', function(){

			return new \OA\PhalconRest\Http\Request;
		});

		$di->set('router', function(){

			return new \Phalcon\Mvc\Router;
		});

		$di->set('response', function(){

			return new \OA\PhalconRest\Http\Response;
		});

		$di->setShared('eventsManager', function(){

			// Create instance
			$eventsManager = new \Phalcon\Events\Manager;

			// Authenticate user
			$eventsManager->attach('micro', new \OA\PhalconRest\Middleware\Authentication);

			// Authorize endpoints
			$eventsManager->attach('micro', new \OA\PhalconRest\Middleware\Acl);

			return $eventsManager;
		});

		$di->setShared('modelsManager', function() use ($di){

			$modelsManager = new \Phalcon\Mvc\Model\Manager();
			$modelsManager->setEventsManager($di->get('eventsManager'));

			return $modelsManager;
		});
	}
}
