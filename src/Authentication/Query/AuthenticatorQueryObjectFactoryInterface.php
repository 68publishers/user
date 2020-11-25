<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Query;

use SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface;

interface AuthenticatorQueryObjectFactoryInterface
{
	/**
	 * @param string $login
	 *
	 * @return \SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface
	 */
	public function create(string $login): QueryObjectInterface;
}
