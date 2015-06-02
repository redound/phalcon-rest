<?php

namespace OA\PhalconRest\Mvc;

use Phalcon\Mvc\Micro as MicroMvc,
	Phaclon\DI\FactoryDefault,
	Phalcon\Http\Response,
	OA\PhalconRest\Exception,
	OA\PhalconRest\UserException,
	OA\PhalconRest\DI\PhalconRestDI,
	OA\PhalconRest\Services\ErrorService as ERR,
	OA\PhalconRest\Http\Request;


class Micro extends MicroMvc
{

	public function __construct(PhalconRestDI $di)
	{
		$vendorPath = __DIR__ . '/../../../../../../vendor/';

		// Manual autoloader
		require_once $vendorPath . 'google/apiclient/autoload.php';
		require_once $vendorPath . 'phpmailer/phpmailer/class.smtp.php';
		require_once $vendorPath . 'phpmailer/phpmailer/class.phpmailer.php';

		// Load dependencies
		$loader = new \Phalcon\Loader();

		$loader->registerDirs([
			$vendorPath . 'phpmailer/phpmailer',
			$vendorPath . 'firebase/php-jwt/Firebase/PHP-JWT/Authentication',
			$vendorPath . 'firebase/php-jwt/Firebase/PHP-JWT/Exceptions'
		]);

		$loader->registerNamespaces([
			'League\Fractal' => $vendorPath . 'league/fractal/src'
		]);

		$loader->register();

	    // Inject di
	    $this->setDI($di);

	    // Set eventsManager
	    $this->setEventsManager($this->eventsManager);

		// Mount Collections
		foreach ($this->config->phalconRest->collections as $collection){

			$this->mount(new $collection);
		}

	    // OPTIONS have no body, send the headers, exit
	    if($this->request->getMethod() == 'OPTIONS'){
	    	$this->response->send([
	    		'result' => 'OK'
	    	]);
	    	exit;
	    }

	    // Handle not found
	    $this->notFound(function() {
	    	throw new Exception(ERR::GEN_NOTFOUND);
	    });

	    $this->before(function() {

	    	$include = $this->request->getQuery('include');

	    	if (!is_null($include)) {
	    	    $this->fractal->parseIncludes($include);
	    	}
	    });

	    $this->after(function() {

	    	// Get return value from controller
	    	$res = $this->getReturnedValue();

	    	$this->response->send($res);
	    });
	}
}
