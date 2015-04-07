<?php

namespace OA\PhalconRest\Transformers;

use League\Fractal;
use OA\PhalconRest\Constants\AccountTypes;

class AccountsTransformer extends TransformerBase
{

	public function transform($accounts)
	{
		return $accounts;
	}
}
