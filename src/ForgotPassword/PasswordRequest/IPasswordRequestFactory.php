<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use SixtyEightPublishers;

interface IPasswordRequestFactory
{
	/**
	 * @param string $email
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\PasswordRequest
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException
	 */
	public function create(string $email): SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest;
}
