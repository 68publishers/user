<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Query;

use SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface;

interface AuthenticatorQueryObjectFactoryInterface
{
	/**
	 * @param string $username
	 *
	 * @return \SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface
	 */
	public function create(string $username): QueryObjectInterface;
}
