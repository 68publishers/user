<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Query;

use Nette;
use Doctrine;
use SixtyEightPublishers;

final class AuthenticatorQueryFactory implements IAuthenticatorQueryFactory
{
	use Nette\SmartObject;

	/** @var \Doctrine\ORM\EntityManagerInterface  */
	private $em;

	/** @var \SixtyEightPublishers\User\Common\UserMapping  */
	private $userMapping;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface          $em
	 * @param \SixtyEightPublishers\User\Common\UserMapping $userMapping
	 */
	public function __construct(Doctrine\ORM\EntityManagerInterface $em, SixtyEightPublishers\User\Common\UserMapping $userMapping)
	{
		$this->em = $em;
		$this->userMapping = $userMapping;
	}

	/************* interface \Nette\Security\IAuthenticator *************/

	/**
	 * {@inheritdoc}
	 */
	public function create(string $login): Doctrine\ORM\Query
	{
		$builder = $this->em->createQueryBuilder();

		$condition = $builder->expr()->eq(
			'u.' . $this->userMapping[$this->userMapping::FIELD_LOGIN],
			':login'
		);

		return $builder
			->select('u')
			->from(SixtyEightPublishers\User\Authentication\DoctrineEntity\IUser::class, 'u')
			->where($condition)
			->setParameter('login', $login)
			->setMaxResults(1)
			->getQuery();
	}
}
