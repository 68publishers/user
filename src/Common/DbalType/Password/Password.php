<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DbalType\Password;

final class Password implements PasswordInterface
{
	/** @var string  */
	private string $value;

	/**
	 * @param string $password
	 * @param bool        $rehash
	 */
	public function __construct(string $password, bool $rehash = TRUE)
	{
		if ($rehash) {
			$passwordHashStrategy = PasswordType::getPasswordHashStrategy();
			$password = $passwordHashStrategy->needRehash($password) ? $passwordHashStrategy->hash($password) : $password;
		}

		$this->value = $password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getValue(): string
	{
		return $this->value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function verify(string $password): bool
	{
		return PasswordType::getPasswordHashStrategy()->verify($password, $this->getValue());
	}

	/**
	 * {@inheritDoc}
	 */
	public function __toString(): string
	{
		return $this->getValue();
	}
}
