<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use Nette;
use SixtyEightPublishers;

final class PasswordHasBeenResetEmail implements IPasswordHasBeenResetEmail
{
	use Nette\SmartObject;

	const NAME = 'password_has_been_reset_email';

	/** @var \SixtyEightPublishers\User\Common\Mail\IMailSender  */
	private $mailSender;

	/**
	 * @param \SixtyEightPublishers\User\Common\Mail\IMailSender $mailSender
	 */
	public function __construct(SixtyEightPublishers\User\Common\Mail\IMailSender $mailSender)
	{
		$this->mailSender = $mailSender;
	}

	/******** interface \SixtyEightPublishers\User\ForgotPassword\Mail\IPasswordResetEmail ********/

	/**
	 * {@inheritdoc}
	 */
	public function send(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user) : void
	{
		$this->mailSender->send(
			self::NAME,
			[
				new SixtyEightPublishers\User\Common\Mail\Address($user->getEmail()),
			],
			[
				'user' => $user,
			]
		);
	}
}
