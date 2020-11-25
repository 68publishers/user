<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface;

interface GetUserByEmailQueryObjectFactoryInterface
{
	/**
	 * @param string $email
	 *
	 * @return \SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface
	 */
	public function create(string $email): QueryObjectInterface;
}
