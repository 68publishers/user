<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DbalType\Password;

final class Password implements PasswordInterface
{
	/** @var string|NULL  */
	private $value;

	/**
	 * @param string|NULL $password
	 * @param bool        $rehash
	 */
	public function __construct(?string $password, bool $rehash = TRUE)
	{
		if (NULL !== $password && $rehash) {
			$passwordHashStrategy = PasswordType::getPasswordHashStrategy();
			$password = $passwordHashStrategy->needRehash($password) ? $passwordHashStrategy->hash($password) : $password;
		}

		$this->value = $password;
	}

	/**
	 * @return \SixtyEightPublishers\User\Common\DbalType\Password\PasswordInterface
	 */
	public static function empty(): PasswordInterface
	{
		return new static(NULL);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getValue(): ?string
	{
		return $this->value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function verify(string $password): bool
	{
		$value = $this->getValue();

		return NULL !== $value ? PasswordType::getPasswordHashStrategy()->verify($password, $value) : FALSE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __toString(): string
	{
		return (string) $this->getValue();
	}
}
