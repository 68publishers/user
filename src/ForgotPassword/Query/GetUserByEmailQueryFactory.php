<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use Nette;
use Doctrine;
use SixtyEightPublishers;

class GetUserByEmailQueryFactory implements IGetUserByEmailQueryFactory
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\User\Common\UserMapping  */
	private $mapping;

	/**
	 * @param \SixtyEightPublishers\User\Common\UserMapping $mapping
	 */
	public function __construct(SixtyEightPublishers\User\Common\UserMapping $mapping)
	{
		$this->mapping = $mapping;
	}

	/********* interface \SixtyEightPublishers\User\ForgotPassword\Query\IGetUserByEmailQueryFactory *********/

	/**
	 * {@inheritdoc}
	 */
	public function create(Doctrine\ORM\EntityManagerInterface $em, string $email): Doctrine\ORM\Query
	{
		return $em->createQueryBuilder()
			->select('u')
			->from(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser::class, 'u')
			->where('u.' . $this->mapping[SixtyEightPublishers\User\Common\UserMapping::FILED_EMAIL] . ' = :email')
			->setParameter('email', $email)
			->setMaxResults(1)
			->getQuery();
	}
}
