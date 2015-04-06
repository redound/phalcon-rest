<?php

namespace OA\PhalconRest\Models;

class UsernameAccounts extends \OA\PhalconRest\Mvc\Model
{

    public $id;
    public $username;
    public $password;
    public $userId;

    public function getSource()
    {

        return 'username_accounts';
    }

    public function columnMap()
    {

        //Keys are the real names in the table and
        //the values their names in the application
        return [
            'id'                        => 'id',
            'username'                  => 'username',
            'password'                  => 'password',
            'user_id'                   => 'userId'
        ];
    }

    public function validateRules()
    {

        return [
            'username' => 'min:6|max:25', // should be between 6 - 25 chars
        ];
    }

}
