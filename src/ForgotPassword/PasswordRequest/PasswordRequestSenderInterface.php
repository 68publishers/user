<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;

interface PasswordRequestSenderInterface
{
	/**
	 * @param string $email
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface|NULL
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException
	 */
	public function send(string $email): ?PasswordRequestInterface;
}
