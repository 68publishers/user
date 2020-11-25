<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use SixtyEightPublishers\DoctrineQueryObjects\AbstractQueryObject;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\DoctrineQueryObjects\QueryFactory\QueryFactoryInterface;

final class FindPasswordRequestByIdsQueryObject extends AbstractQueryObject
{
	/** @var mixed  */
	private $userId;

	/** @var mixed  */
	private $passwordRequestId;

	/**
	 * @param mixed $userId
	 * @param mixed $passwordRequestId
	 */
	public function __construct($userId, $passwordRequestId)
	{
		$this->userId = $userId;
		$this->passwordRequestId = $passwordRequestId;
	}

	/**
	 * {@inheritdoc}
	 */
	public function createQuery(QueryFactoryInterface $queryFactory)
	{
		return $queryFactory->createQueryBuilder()
			->select('pr')
			->from(PasswordRequestInterface::class, 'pr')
			->where('pr.user = :uid')
			->andWhere('pr.id = :rid')
			->andWhere('pr.status = :status')
			->setParameter('uid', $this->userId)
			->setParameter('rid', $this->passwordRequestId)
			->setParameter('status', PasswordRequestInterface::STATUS_CREATED);
	}
}
