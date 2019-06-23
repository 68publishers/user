<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use SixtyEightPublishers;

interface IPasswordRequestSender
{
	/**
	 * @param string $email
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest|NULL
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException
	 */
	public function send(string $email) : ?SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest;
}
