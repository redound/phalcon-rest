<?php

namespace PhalconRest\Auth\Account;

use PhalconRest\Constants\Services as PhalconRestServices;
use PhalconRest\Constants\ErrorCodes as ErrorCodes;
use PhalconRest\Exceptions\UserException;

class Email extends \Phalcon\Mvc\User\Plugin implements \PhalconRest\Auth\Account
{
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setUserModel(\Phalcon\Mvc\Model $userModel)
    {
        $this->userModel = get_class($userModel);
    }

    public function setEmailAccountModel(\Phalcon\Mvc\Model $emailAccountModel)
    {
        $this->emailAccountModel = get_class($emailAccountModel);
    }

    public function setMailService($mailService)
    {
        $this->mailService = $this->di->get($mailService);
    }

    public function register($data)
    {
        $userModel = $this->userModel;
        $emailAccountModel = $this->emailAccountModel;
        $authManager = $this->di->get(PhalconRestServices::AUTH_MANAGER);


        $db = $this->di->get('db');

        if (!isset($data->name) && !isset($data->email) || !isset($data->password)) {

            throw new UserException(ErrorCodes::DATA_INVALID);
        }

        if (strlen($data->password) > 25 || strlen($data->password) < 5) {

            throw new UserException(ErrorCodes::DATA_INVALID);
        } 

        $user = $userModel::findFirstByEmail($data->email);

        // When user already exists with email account
        if ($user && $user->getAccount($this->name)) {

            throw new UserException(ErrorCodes::DATA_DUPLICATE, 'User already exists.');
        }

        // Check if perhaps email already exists
        $emailAccount = $emailAccountModel::findFirstByEmail($data->email);

        if ($emailAccount) {

           throw new UserException(ErrorCodes::DATA_DUPLICATE, 'Email already exists');
        }

        // Let's create email account
        $emailAccount = new $emailAccountModel();
        $emailAccount->email = $data->email;
        $emailAccount->password = $this->security->hash($data->password);

        // If user already exists, this stays false in the
        // next check so there will not be sent an activation mail.
        $sendActivationMail = false;

        // Here we start a transaction, because we are possibly executing
        // multiple queries. If one fails, we simply rollback all the queries
        // so there won't be any data inconsistency
        $db->begin();

        try {

            // If there's no user yet, first create one.
            if (!$user) {

                $sendActivationMail = true;

                $mailToken = $authManager->createMailToken();

                $user = new $this->userModel();
                $user->name = $data->name;
                $user->email = $data->email;

                // By default, user is not active.
                // They need to click the mailToken they get sent by mail
                $user->active = 0;
                $user->mailToken = $mailToken;
            }

            $user->emailAccount = $emailAccount;

            if (!$user->save()) {

                throw new \Exception('User could not be registered.');
            }

            if ($sendActivationMail) {

                // Send a mail where they can activate their account
                $sent = $this->mailService->sendActivationMail($user, $emailAccount);

                if (!$sent) {

                    throw new \Exception('User #' . $user->id . ' was created, but Activation mail could not be sent. So changes have been rolled back.');
                }
            }

            // Everything has gone to plan, let's commit those changes!
            $db->commit();

        } catch (\Exception $e) {

            $db->rollback();
            throw new UserException(ErrorCodes::USER_CREATEFAIL, $e->getMessage());
        }

        return $user;
    }

    public function changepassword($data)
    {
        $authManager = $this->di->get(PhalconRestServices::AUTH_MANAGER);
        $user = $authManager->getUser();

        $user = \User::findFirst($user->id);

        if (!$user || !$emailAccount = $user->getAccount($this->name)) {

            throw new UserException(ErrorCodes::USER_NOTFOUND);
        }

        if (!isset($data->oldPassword) || !isset($data->newPassword)) {

            throw new UserException(ErrorCodes::DATA_INVALID, 'Both oldPassword as newPassword are required.');
        }

        // Check if password is valid
        if (!$emailAccount->validatePassword($data->oldPassword)) {
            
            throw new UserException(ErrorCodes::DATA_INVALID, 'oldPassword not valid.');
        }

        $emailAccount->password = $this->security->hash($data->newPassword);

        if (!$emailAccount->save()) {

            throw new UserException(ErrorCodes::DATA_UPDATE_FAIL, 'Could not change password');
        }

        return $user;
    }

    public function login($email = null, $password = null)
    {
        $emailAccountModel = $this->emailAccountModel;

        $emailAccount = $emailAccountModel::findFirstByEmail($email);

        // Check if password is valid
        if (!$emailAccount || !$emailAccount->validatePassword($password)) {
            return false;
        }

        // Something is terribly wrong, can't find the real user
        if (!$user = $emailAccount->user) {
            return false;
        }

        if ($emailAccount->user->active != 1) {

            throw new UserException(ErrorCodes::USER_NOTACTIVE, 'User should be activated first');
        }

        return $user;
    }
}
