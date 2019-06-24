<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use Nette;
use Doctrine;
use SixtyEightPublishers;

class CancelPasswordRequestsByUserQueryFactory implements ICancelPasswordRequestsByUserQueryFactory
{
	use Nette\SmartObject;

	/********* interface \SixtyEightPublishers\User\ForgotPassword\Query\IGetUserByEmailQueryFactory *********/

	/**
	 * {@inheritdoc}
	 */
	public function create(Doctrine\ORM\EntityManagerInterface $em, SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user): Doctrine\ORM\Query
	{
		return $em->createQueryBuilder()
			->update(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest::class, 'pr')
			->set('pr.status', ':newStatus')
			->set('pr.updated', ':now')
			->where('pr.user = :user')
			->andWhere('pr.status = :status')
			->setParameter('user', $user->getId())
			->setParameter('status', SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest::STATUS_CREATED)
			->setParameter('newStatus', SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest::STATUS_CANCELED)
			->setParameter('now', new \DateTime('now', new \DateTimeZone('UTC')))
			->getQuery();
	}
}
