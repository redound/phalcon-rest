<?php

namespace OA\PhalconRest\Transformers;

use League\Fractal;

class UsernameAccountTransformer extends Fractal\TransformerAbstract
{
	public function transform($account)
	{
		return [
			'id' 				=> (int) $account->id,
			'userId' 			=> (int) $account->userId,
			'username' 			=> $account->username
		];
	}
}
