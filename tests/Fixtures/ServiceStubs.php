<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Tests\Fixtures;

use LogicException;
use Doctrine\ORM\EntityManagerInterface;
use Contributte\Mailing\IMailBuilderFactory;
use SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface;
use SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface;
use SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectInterface;
use SixtyEightPublishers\DoctrineQueryObjects\ResultSet\ResultSetOptionsInterface;
use SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface;

/**
 * Services of the application that are not created by the tested code.
 */
final class ServiceStubs
{
	public static function entityManager(): EntityManagerInterface
	{
		throw new LogicException('Not implemented.');
	}

	public static function transactionFactory(): TransactionFactoryInterface
	{
		throw new LogicException('Not implemented.');
	}

	public static function executableQueryObjectFactory(): ExecutableQueryObjectFactoryInterface
	{
		return new class implements ExecutableQueryObjectFactoryInterface {
			public function create(QueryObjectInterface $queryObject, ?ResultSetOptionsInterface $resultSetOptions = NULL): ExecutableQueryObjectInterface
			{
				throw new LogicException('Not implemented.');
			}
		};
	}

	public static function mailBuilderFactory(): IMailBuilderFactory
	{
		throw new LogicException('Not implemented.');
	}
}
