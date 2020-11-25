<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface;

interface PasswordHasBeenResetEmailInterface
{
	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface $user
	 *
	 * @return void
	 */
	public function send(UserInterface $user): void;
}
