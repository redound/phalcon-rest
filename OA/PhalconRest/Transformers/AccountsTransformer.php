<?php

namespace OA\PhalconRest\Transformers;

use League\Fractal;
use OA\PhalconRest\Constants\AccountTypes;

class AccountsTransformer extends Fractal\TransformerAbstract
{

	public function transform($accounts)
	{
		return $accounts;
	}
}
