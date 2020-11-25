<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface;

interface FindPasswordRequestByIdsQueryObjectFactoryInterface
{
	/**
	 * @param mixed $userId
	 * @param mixed $passwordRequestId
	 *
	 * @return \SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface
	 */
	public function create($userId, $passwordRequestId): QueryObjectInterface;
}
