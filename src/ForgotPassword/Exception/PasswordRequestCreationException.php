<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Exception;

use SixtyEightPublishers;

final class PasswordRequestCreationException extends SixtyEightPublishers\User\Common\Exception\RuntimeException
{
	const   CODE_NOT_REGISTERED_EMAIL = 1001;

	/**
	 * @param string $email
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException
	 */
	public static function notRegisteredEmail(string $email): self
	{
		return new static(sprintf(
			'Email "%s" is not registered',
			$email
		), self::CODE_NOT_REGISTERED_EMAIL);
	}

	/**
	 * @param \Throwable $e
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException
	 */
	public static function from(\Throwable $e): self
	{
		return !$e instanceof self ? new static($e->getMessage(), $e->getCode(), $e) : $e;
	}

	/**
	 * @return bool
	 */
	public function isNotRegisteredEmail(): bool
	{
		return $this->getCode() === self::CODE_NOT_REGISTERED_EMAIL;
	}
}
