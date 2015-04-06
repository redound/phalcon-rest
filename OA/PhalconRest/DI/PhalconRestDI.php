<?php

namespace OA\PhalconRest\DI;

class PhalconRestDI extends \Phalcon\DI\FactoryDefault
{

	public function __construct($config)
	{
		// Set up default services
		parent::__construct();

		$di = $this;

		$di->setShared('config', function() use ($config){

			return $config;
		});

		$di->set('fractal', function(){

			$fractal = new \League\Fractal\Manager;
			$fractal->setSerializer(new \OA\Fractal\CustomSerializer);
			return $fractal;
		});

		$di->setShared('phpmailer', function() use ($di){

			$phpmailer = $di->get('config')->phalconRest->phpmailer;

			//Create a new PHPMailer instance
			$mail = new \PHPMailer;

			//Tell PHPMailer to use SMTP
			$mail->isSMTP();

			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = $phpmailer->debugMode;

			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';

			//Set the hostname of the mail server
			$mail->Host = $phpmailer->host;

			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port = $phpmailer->port;

			//Set the encryption system to use - ssl (deprecated) or tls
			$mail->SMTPSecure = $phpmailer->smtpSecure;

			//Whether to use SMTP authentication
			$mail->SMTPAuth = $phpmailer->smtpAuth;

			//Username to use for SMTP authentication - use full email address for gmail
			$mail->Username = $phpmailer->username;

			//Password to use for SMTP authentication
			$mail->Password = $phpmailer->password;

			//Set who the message is to be sent from
			$mail->setFrom($phpmailer->from[0], $phpmailer->from[1]);

			//Set an alternative reply-to address
			$mail->addReplyTo($phpmailer->replyTo[0], $phpmailer->replyTo[1]);

			//Set the subject line
			$mail->Subject = 'No subject';

			return $mail;
		});

		$di->setShared('authservice', function(){

			return new \OA\PhalconRest\Services\AuthService;
		});

		$di->setShared('mailservice', function(){

			return new \OA\PhalconRest\Services\MailService;
		});

		$di->setShared('userService', function(){

			return new \OA\PhalconRest\Services\UserService;
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
