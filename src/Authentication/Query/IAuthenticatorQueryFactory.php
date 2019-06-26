<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Query;

use Doctrine;

interface IAuthenticatorQueryFactory
{
	/**
	 * @param string $login
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function create(string $login): Doctrine\ORM\Query;
}
