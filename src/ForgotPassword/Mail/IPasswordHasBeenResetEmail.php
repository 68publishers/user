<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use SixtyEightPublishers;

interface IPasswordHasBeenResetEmail
{
	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user
	 *
	 * @return void
	 */
	public function send(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user) : void;
}
