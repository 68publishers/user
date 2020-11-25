<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\PasswordHashStrategy;

use Nette\Security\Passwords;

final class DefaultPasswordHashStrategy implements PasswordHashStrategyInterface
{
	/** @var \Nette\Security\Passwords  */
	private $passwords;

	/**
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		$this->passwords = new Passwords(PASSWORD_DEFAULT, $options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function hash(string $password): string
	{
		return $this->passwords->hash($password);
	}

	/**
	 * {@inheritdoc}
	 */
	public function needRehash(string $password): bool
	{
		return $this->passwords->needsRehash($password);
	}

	/**
	 * {@inheritdoc}
	 */
	public function verify(string $password, string $hash): bool
	{
		return $this->passwords->verify($password, $hash);
	}
}
