<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use Doctrine;

interface IFindPasswordRequestByIdsQueryFactory
{
	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 * @param mixed                                $userId
	 * @param mixed                                $passwordRequestId
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function create(Doctrine\ORM\EntityManagerInterface $em, $userId, $passwordRequestId): Doctrine\ORM\Query;
}
