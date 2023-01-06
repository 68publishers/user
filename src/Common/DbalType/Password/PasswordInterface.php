<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DbalType\Password;

interface PasswordInterface
{
	/**
	 * @return string
	 */
	public function getValue(): string;

	/**
	 * @param string $password
	 *
	 * @return bool
	 */
	public function verify(string $password): bool;

	/**
	 * @return string
	 */
	public function __toString(): string;
}
