<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\QueryBuilder;
use SixtyEightPublishers\DoctrineQueryObjects\AbstractQueryObject;
use SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\DoctrineQueryObjects\QueryFactory\QueryFactoryInterface;

final class CancelPasswordRequestsByUserQueryObject extends AbstractQueryObject
{
	/** @var \SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface  */
	private $user;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface $user
	 */
	public function __construct(UserInterface $user)
	{
		$this->user = $user;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Exception
	 */
	public function createQuery(QueryFactoryInterface $queryFactory): QueryBuilder
	{
		return $queryFactory->createQueryBuilder()
			->update(PasswordRequestInterface::class, 'pr')
			->set('pr.status', ':newStatus')
			->set('pr.updated', ':now')
			->where('pr.user = :user')
			->andWhere('pr.status = :status')
			->setParameter('user', $this->user->getId())
			->setParameter('status', PasswordRequestInterface::STATUS_CREATED)
			->setParameter('newStatus', PasswordRequestInterface::STATUS_CANCELED)
			->setParameter('now', new DateTime('now', new DateTimeZone('UTC')));
	}
}
