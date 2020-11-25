<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use SixtyEightPublishers\User\Common\UserMapping;
use SixtyEightPublishers\DoctrineQueryObjects\AbstractQueryObject;
use SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface;
use SixtyEightPublishers\DoctrineQueryObjects\QueryFactory\QueryFactoryInterface;

final class GetUserByEmailQueryObject extends AbstractQueryObject
{
	/** @var string  */
	private $email;

	/** @var \SixtyEightPublishers\User\Common\UserMapping  */
	private $mapping;

	/**
	 * @param string                                        $email
	 * @param \SixtyEightPublishers\User\Common\UserMapping $mapping
	 */
	public function __construct(string $email, UserMapping $mapping)
	{
		$this->email = $email;
		$this->mapping = $mapping;
	}

	/**
	 * {@inheritdoc}
	 */
	public function createQuery(QueryFactoryInterface $queryFactory)
	{
		return $queryFactory->createQueryBuilder()
			->select('u')
			->from(UserInterface::class, 'u')
			->where('u.' . $this->mapping[UserMapping::FILED_EMAIL] . ' = :email')
			->setParameter('email', $this->email);
	}
}
