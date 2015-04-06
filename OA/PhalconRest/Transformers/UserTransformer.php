<?php

namespace OA\PhalconRest\Transformers;

use League\Fractal,
    OA\PhalconRest\Constants\AccountTypes;

class UserTransformer extends Fractal\TransformerAbstract
{

	protected $availableIncludes = [
		'accounts'
	];

	public function transform($user)
	{
		return [
			'id' 				=> (int) $user->id,
			'name' 				=> $user->name,
			'email' 			=> $user->email,
			'dateRegistered' 	=> (int) strtotime($user->dateRegistered) * 1000,
			'active'			=> (int) $user->active,
			'accountTypeIds' 	=> $user->accountTypeIds
		];
	}

	public function includeAccounts($user){

		return $this->item($user->getAccounts(), new AccountsTransformer, 'parent');
	}
}
