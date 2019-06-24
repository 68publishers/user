<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use SixtyEightPublishers;

interface IForgotPasswordResetEmail
{
	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request
	 *
	 * @return void
	 */
	public function send(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request): void;
}
