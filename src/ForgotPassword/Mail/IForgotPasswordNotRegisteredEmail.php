<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

interface IForgotPasswordNotRegisteredEmail
{
	/**
	 * @param string $email
	 *
	 * @return void
	 */
	public function send(string $email): void;
}
