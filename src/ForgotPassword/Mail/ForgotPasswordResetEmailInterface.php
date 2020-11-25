<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;

interface ForgotPasswordResetEmailInterface
{
	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface $request
	 *
	 * @return void
	 */
	public function send(PasswordRequestInterface $request): void;
}
