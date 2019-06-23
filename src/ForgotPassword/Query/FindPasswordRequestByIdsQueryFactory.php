<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use Nette;
use Doctrine;
use SixtyEightPublishers;

class FindPasswordRequestByIdsQueryFactory implements IFindPasswordRequestByIdsQueryFactory
{
	use Nette\SmartObject;

	/********* interface \SixtyEightPublishers\User\ForgotPassword\Query\IGetUserByEmailQueryFactory *********/

	/**
	 * {@inheritdoc}
	 */
	public function create(Doctrine\ORM\EntityManagerInterface $em, $userId, $passwordRequestId) : Doctrine\ORM\Query
	{
		return $em->createQueryBuilder()
			->select('pr')
			->from(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest::class, 'pr')
			->where('pr.user = :uid')
			->andWhere('pr.id = :rid')
			->andWhere('pr.status = :status')
			->setParameter('uid', $userId)
			->setParameter('rid', $passwordRequestId)
			->setParameter('status', SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest::STATUS_CREATED)
			->setMaxResults(1)
			->getQuery();
	}
}
