<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Query;

use Doctrine\ORM\QueryBuilder;
use SixtyEightPublishers\User\Common\UserMapping;
use SixtyEightPublishers\DoctrineQueryObjects\AbstractQueryObject;
use SixtyEightPublishers\User\Authentication\Entity\UserInterface;
use SixtyEightPublishers\DoctrineQueryObjects\QueryFactory\QueryFactoryInterface;

final class AuthenticatorQueryObject extends AbstractQueryObject
{
	/** @var string  */
	private $username;

	/** @var \SixtyEightPublishers\User\Common\UserMapping  */
	private $userMapping;

	/**
	 * @param string                                        $username
	 * @param \SixtyEightPublishers\User\Common\UserMapping $userMapping
	 */
	public function __construct(string $username, UserMapping $userMapping)
	{
		$this->username = $username;
		$this->userMapping = $userMapping;
	}

	/**
	 * {@inheritDoc}
	 */
	public function createQuery(QueryFactoryInterface $queryFactory): QueryBuilder
	{
		$builder = $queryFactory->createQueryBuilder();

		$condition = $builder->expr()->eq(
			'u.' . $this->userMapping[$this->userMapping::FIELD_USERNAME],
			':username'
		);

		return $builder
			->select('u')
			->from(UserInterface::class, 'u')
			->where($condition)
			->setParameter('username', $this->username);
	}
}
