<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\PasswordHashStrategy;

use Nette;

final class DefaultPasswordHashStrategy implements IPasswordHashStrategy
{
	use Nette\SmartObject;

	/** @var array  */
	private $options;

	/**
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		$this->options = $options;
	}

	/*********** interface \SixtyEightPublishers\User\Common\PasswordHashStrategy\IPasswordHashStrategy ***********/

	/**
	 * {@inheritdoc}
	 */
	public function hash(string $password): string
	{
		return Nette\Security\Passwords::hash($password, $this->options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function needRehash(string $password): bool
	{
		return Nette\Security\Passwords::needsRehash($password, $this->options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function verify(string $password, string $hash): bool
	{
		return Nette\Security\Passwords::verify($password, $hash);
	}
}
