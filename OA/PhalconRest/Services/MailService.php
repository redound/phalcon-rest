<?php

namespace OA\PhalconRest\Services;

use OA\PhalconRest\CoreException,
	OA\PhalconRest\UserException,
	OA\PhalconRest\Services\ErrorService as ERR;

class MailService extends \Phalcon\Mvc\User\Plugin
{

	public function sendActivationMail($user, $account)
	{

		$mail = $this->phpmailer;
		$mail->Subject = 'Activate your Account';
		$mail->addAddress($user->email, $user->name);

		// Render mail template
		$view = $this->view;
		$view->setVar('user', $user);
		$view->setVar('account', $account);
		$renderedView = $view->render('mail/activation');

		// Add template to mail body
		$mail->msgHTML($renderedView);

		return $mail->send();
	}
}
