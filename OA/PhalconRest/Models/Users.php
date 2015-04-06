<?php

namespace OA\PhalconRest\Models;

use OA\PhalconRest\CoreException;
use OA\PhalconRest\Constants\AccountTypes;
use League\Fractal\Resource\Item;

class Users extends \OA\PhalconRest\Mvc\Model
{

    public function getSource()
    {

        return 'users';
    }

    public function columnMap()
    {
        //Keys are the real names in the table and
        //the values their names in the application
        return [
            'id'                        => 'id',
            'name'                      => 'name',
            'email'                     => 'email',
            'date_registered'           => 'dateRegistered',
            'account_type_ids'          => 'accountTypeIds',
            'active'                    => 'active',
            'mail_token'                => 'mailToken',
            'updated_at'                => 'updatedAt',
            'created_at'                => 'createdAt',
        ];
    }

    public function initialize()
    {
        $this->hasOne('id', 'GoogleAccounts', 'userId');
        $this->hasOne('id', 'UsernameAccounts', 'userId');
    }

    public function getAccountTypes()
    {

        if (!isset($this->accountTypeIds)) {

            return [];
        }

        return explode(',', $this->accountTypeIds);
    }

    public function setAccountTypes($types)
    {

        $this->accountTypeIds = implode(',', $types);
    }

    public function hasAccountType($type)
    {

        $accountTypes = $this->getAccountTypes();
        return (in_array($type, $accountTypes));
    }

    public function addAccountType($type)
    {

        if (!$this->hasAccountType($type)) {

            $accountTypes = $this->getAccountTypes();
            $accountTypes[] = $type;
            $this->setAccountTypes($accountTypes);
        }
    }

    public function getAccounts()
    {

        $accounts = [];

        $di = \Phalcon\DI::getDefault();
        $fractal = $di->get('fractal');

        if ($this->googleAccounts){
            $account = $fractal->createData(new Item($this->googleAccounts, new GoogleAccountTransformer, 'google'))->toArray();
            $accounts = array_merge($accounts, $account);
        }

        if ($this->usernameAccounts){
            $account = $fractal->createData(new Item($this->usernameAccounts, new UsernameAccountTransformer, 'username'))->toArray();
            $accounts = array_merge($accounts, $account);
        }
        return $accounts;
    }

    public function removeAccountType()
    {

        if ($this->hasAccountType($type)) {

            $accountTypes = $this->getAccountTypes();
            unset($accountTypes[$type]);
            $this->setAccountTypes($accountTypes);
        }
    }

    public function validateRules()
    {

        return [
            'name' => 'pattern:/[A-Za-z ]{2,55}/', // should contain between 2 - 55 letters
            'email' => 'email', // should be an email address
        ];
    }

    public function beforeValidationOnCreate()
    {

        parent::beforeValidationOnCreate();
        $this->dateRegistered = date('Y-m-d H:i:s');
    }
}
