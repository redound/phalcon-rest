<?php

namespace PhalconRest\Services;

use PhalconRest\Exceptions\UserException,
    PhalconRest\Exceptions\CoreException,
    PhalconRest\Services\AuthService,
    PhalconRest\Constants\AccountTypes,
    OA\Phalcon\Validation\Validator,
    Library\PhalconRest\Constants\ErrorCodes as ErrorCodes,
    PhalconRest\Models\Users,
    PhalconRest\Models\UsernameAccounts;

class UserService extends \Phalcon\Mvc\User\Plugin {

    public function activate()
    {

        if (!$this->request->hasQuery('mailtoken')) {

            throw new UserException(ErrorCodes::GEN_NOTFOUND, 'You will need a mailtoken to activate');
        }

        $mailtoken = $this->request->getQuery('mailtoken');

        $user = Users::findFirst([
            'conditions' => 'mailToken = :mailtoken:',
            'bind' => ['mailtoken' => $mailtoken]
        ]);

        if (!$user) {

            throw new UserException(ErrorCodes::DATA_NOTFOUND, 'Could not activate, user not found');
        }

        // Activate user by resetting mailToken && setting active to 1
        $user->mailToken = null;
        $user->active = 1;

        if (!$user->save()) {

            throw new UserException(ErrorCodes::DATA_FAIL, 'User found, but could not activate.');
        }

        return $user;
    }

    public function register($data)
    {

        $user = Users::findFirstByEmail($data->email);

        // When user already exists with username account
        if ($user && $user->hasAccountType(AccountTypes::USERNAME)) {

            throw new UserException(ErrorCodes::DATA_DUPLICATE, 'User already exists.');
        }

        // If user already exists, this stays false in the
        // next check so there will not be sent an activation mail.
        $sendActivationMail = false;

        // Here we start a transaction, because we are possibly executing
        // multiple queries. If one fails, we simply rollback all the queries
        // so there won't be any data inconsistency
        $this->db->begin();

        // Also, we need prevent validation exceptions
        // When an validation error occurs an exception
        // would be thrown and the previous changes
        // not be rolled back.
        $this->config->noValidationExceptions = true;

        try {


            // If there's no user yet, first create one.
            if (!$user) {

                $sendActivationMail = true;

                $mailToken = $this->security->hash($this->config->phalconRest->genSalt . rand(0,10));

                $user = new Users();
                $user->name             = $data->name;
                $user->email            = $data->email;
                $user->addAccountType(AccountTypes::USERNAME);

                // By default, user is not active.
                // They need to click the mailToken they get sent by mail
                $user->active           = 0;
                $user->mailToken        = $mailToken;


                if (!$user->save()) {

                    throw new \Exception('User could not be created');
                }
            }

            // Manual validation username and password
            $validator = Validator::make($data->account, [
                'username' => 'min:6|required',
                'password' => 'min:6|required'
            ]);

            if ($validator->fails()){

                throw new \Exception($validator->getFirstMessage());
            }

            // Check if perhaps username already exists
            $usernameAccount = UsernameAccounts::findFirstByUsername($data->account->username);

            if ($usernameAccount){

                throw new \Exception('Username already exists');
            }

            // Let's create username account
            $usernameAccount                        = new UsernameAccounts();
            $usernameAccount->username              = $data->account->username;
            $usernameAccount->password              = $this->security->hash($data->account->password);
            $usernameAccount->userId                = $user->id;

            if (!$usernameAccount->save()) {

                throw new \Exception('Username account for user #' . $user->id . ' could not be created.');
            }

            // UsernameAccount created, let's reflect that on the user
            $user->addAccountType(AccountTypes::USERNAME);
            if (!$user->save()) {

                throw new \Exception('Username account could not be bind to user.');
            }

            if ($sendActivationMail) {

                // Send a mail where they can activate their account
                $sent = $this->mailservice->sendActivationMail($user, $usernameAccount);

                if (!$sent){

                    throw new \Exception('User #' . $user->id . ' was created, but Activation mail could not be sent. So changes have been rolled back.');
                }
            }

            // Everything has gone to plan, let's commit those changes!
            $this->db->commit();

        } catch(\Exception $e){

            $this->db->rollback();
            throw new UserException(ErrorCodes::USER_CREATEFAIL, $e->getMessage());
        }

        return $user;
    }

    public function me()
    {

        $user = $this->authservice->getUser();

        $user = Users::findFirst($user->id);

        if (!$user){

            throw new UserException(ErrorCodes::USER_NOTFOUND);
        }

        return $user;
    }

    public function login()
    {

        // First check if bearer is present in Authentication header
        // eg. Google NC8tc0o1bFFaREl6NFJLRGpOTkR3ZkRoQzNHMUw5YV
        // eg. Username NC8tc0o1bFFaREl6NFJLRGpOTkR3ZkRoQzNHMUw5YV
        if (is_null($bearer = $this->request->getBearer())) {
            throw new UserException(ErrorCodes::AUTH_NOBEARER);
        };

        $username = $this->request->getAuth()['username'];
        $password = $this->request->getAuth()['password'];

        if (is_null($username)) {

            throw new UserException(ErrorCodes::AUTH_NOUSERNAME);
        };

        if (!$this->authservice->login($bearer, $username, $password)) {

            throw new UserException(ErrorCodes::AUTH_BADLOGIN, 'Failed to login.');
        }

        $token = $this->authservice->createToken();

        $tokenData = [
            'AuthToken' => \JWT::encode($token, $this->config->phalconRest->jwtSecret),
            'Expires' => $token['exp']
        ];

        return $tokenData;
    }
}
