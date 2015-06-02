<?php

namespace OA\PhalconRest\Http;

use OA\PhalconRest\Services\ErrorService;

class Response extends \Phalcon\Mvc\User\Plugin {

	protected $statusCode = 200;
	protected $envelope = true;

	public function sendException($e)
	{

		$code = $e->getKey();
		$message = $e->getMessage();

		// Use key to obtain status code
		$this->statusCode = ErrorService::getStatusCode($code);

		// Use key to obtain response message
		$message = ErrorService::getMessage($code);

		$this->send([
			'error' => [
				'code' => $code,
				'status' => $this->statusCode,
				'developer' => $e->getMessage(),
				'message' => $message
			]
		]);
	}

	public function send($data)
	{

		$res = new \Phalcon\Http\Response;

		$res->setHeader('Content-Type', 'application/json');
		$res->setHeader('Access-Control-Allow-Origin', '*');
		$res->setHeader('Access-Control-Allow-Methods', 'GET,HEAD,PUT,PATCH,POST,DELETE');
	    $res->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
		$res->setHeader('E-Tag', md5(serialize($data)));
		$res->setStatusCode($this->statusCode, $this->getMessage($this->statusCode));
		$res->setContentType('application/json');
		$res->setJsonContent($data);
		$res->send();
		exit;
	}

	protected function getMessage($code)
	{

		$codes = [
			// Informational 1xx
			100 => 'Continue',
			101 => 'Switching Protocols',
			// Success 2xx
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			// Redirection 3xx
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found', // 1.1
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			// 306 is deprecated but reserved
			307 => 'Temporary Redirect',
			// Client Error 4xx
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			// Server Error 5xx
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			509 => 'Bandwidth Limit Exceeded'
		];

		return (isset($codes[$code])) ? $codes[$code] : 'Unknown Status Code';
	}
}
