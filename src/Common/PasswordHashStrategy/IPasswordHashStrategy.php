<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\PasswordHashStrategy;

interface IPasswordHashStrategy
{
	/**
	 * @param string $password
	 *
	 * @return string
	 */
	public function hash(string $password): string;

	/**
	 * @param string $password
	 *
	 * @return bool
	 */
	public function needRehash(string $password): bool;

	/**
	 * @param string $password
	 * @param string $hash
	 *
	 * @return bool
	 */
	public function verify(string $password, string $hash): bool;
}
