<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use Nette;
use Doctrine;
use SixtyEightPublishers;

class GetUserByEmailQueryFactory implements IGetUserByEmailQueryFactory
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\User\Common\UserMappingFields  */
	private $fields;

	/**
	 * @param \SixtyEightPublishers\User\Common\UserMappingFields $fields
	 */
	public function __construct(SixtyEightPublishers\User\Common\UserMappingFields $fields)
	{
		$this->fields = $fields;
	}

	/********* interface \SixtyEightPublishers\User\ForgotPassword\Query\IGetUserByEmailQueryFactory *********/

	/**
	 * {@inheritdoc}
	 */
	public function create(Doctrine\ORM\EntityManagerInterface $em, string $email) : Doctrine\ORM\Query
	{
		return $em->createQueryBuilder()
			->select('u')
			->from(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser::class, 'u')
			->where('u.' . $this->fields[SixtyEightPublishers\User\Common\UserMappingFields::FILED_EMAIL] . ' = :email')
			->setParameter('email', $email)
			->setMaxResults(1)
			->getQuery();
	}
}
