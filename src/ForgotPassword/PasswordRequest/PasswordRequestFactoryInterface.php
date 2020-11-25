<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;

interface PasswordRequestFactoryInterface
{
	/**
	 * @param string $email
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequest
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException
	 */
	public function create(string $email): PasswordRequestInterface;
}
