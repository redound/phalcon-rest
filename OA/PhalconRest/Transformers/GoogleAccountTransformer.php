<?php

namespace OA\PhalconRest\Transformers;

use League\Fractal;

class GoogleAccountTransformer extends Fractal\TransformerAbstract
{
	public function transform($account)
	{
		return [
			'id' 				=> (int) $account->id,
			'userId' 			=> (int) $account->userId,
			'googleId' 			=> (int) $account->googleId,
			'email' 			=> $account->email
		];
	}
}
