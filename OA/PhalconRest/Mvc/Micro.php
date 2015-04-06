<?php

namespace OA\PhalconRest\Mvc;

use Phalcon\Mvc\Micro as MicroMvc,
	Phaclon\DI\FactoryDefault,
	Phalcon\Http\Response,
	OA\PhalconRest\Exception,
	OA\PhalconRest\DI\PhalconRestDI,
	OA\PhalconRest\Services\ErrorService as ERR,
	OA\PhalconRest\Http\Request;


class Micro extends MicroMvc
{

	public function __construct(PhalconRestDI $di)
	{

	    // Inject di
	    $this->setDI($di);

	    // Set eventsManager
	    $this->setEventsManager($this->eventsManager);

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
