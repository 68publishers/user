<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DbalType\Password;

use Nette\DI\Container;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use SixtyEightPublishers\User\Common\Exception\RuntimeException;
use SixtyEightPublishers\DoctrineBridge\Type\ContainerAwareTypeInterface;
use SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface;

final class PasswordType extends StringType implements ContainerAwareTypeInterface
{
	/** @var \SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface|NULL */
	private static $passwordHashStrategy;

	/**
	 * @internal
	 *
	 * {@inheritDoc}
	 */
	public function setContainer(Container $container, array $context = []): void
	{
		self::setPasswordHashStrategy($container->getByType(PasswordHashStrategyInterface::class));
	}

	/**
	 * @param \SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface $passwordHashStrategy
	 *
	 * @return void
	 */
	public static function setPasswordHashStrategy(PasswordHashStrategyInterface $passwordHashStrategy): void
	{
		self::$passwordHashStrategy = $passwordHashStrategy;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return PasswordInterface::class;
	}

	/**
	 * {@inheritDoc}
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform): ?Password
	{
		$value = parent::convertToPHPValue($value, $platform);

		return null !== $value ? new Password($value, FALSE) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
	{
		if ($value instanceof Password) {
			$value = $value->getValue();
		}

		$passwordHashStrategy = self::getPasswordHashStrategy();

		if (NULL !== $value && $passwordHashStrategy->needRehash($value)) {
			$value = self::$passwordHashStrategy->hash($value);
		}

		return parent::convertToDatabaseValue($value, $platform);
	}

	/**
	 * @return \SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface
	 * @throws \SixtyEightPublishers\User\Common\Exception\RuntimeException
	 */
	public static function getPasswordHashStrategy(): PasswordHashStrategyInterface
	{
		if (NULL === self::$passwordHashStrategy) {
			throw new RuntimeException(sprintf(
				'Password hash strategy is not set. Please call method %s::setPasswordHashStrategy().',
				static::class
			));
		}

		return self::$passwordHashStrategy;
	}
}
