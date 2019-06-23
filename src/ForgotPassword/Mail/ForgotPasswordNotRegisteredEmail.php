<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use Nette;
use SixtyEightPublishers;

final class ForgotPasswordNotRegisteredEmail implements IForgotPasswordNotRegisteredEmail
{
	use Nette\SmartObject;

	const NAME = 'forgot_password_not_registered_email';

	/** @var \SixtyEightPublishers\User\Common\Mail\IMailSender  */
	private $mailSender;

	/**
	 * @param \SixtyEightPublishers\User\Common\Mail\IMailSender $mailSender
	 */
	public function __construct(SixtyEightPublishers\User\Common\Mail\IMailSender $mailSender)
	{
		$this->mailSender = $mailSender;
	}

	/******** interface \SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordNotRegisteredEmail ********/

	/**
	 * {@inheritdoc}
	 */
	public function send(string $email) : void
	{
		$this->mailSender->send(
			self::NAME,
			[
				new SixtyEightPublishers\User\Common\Mail\Address($email),
			],
			[
				'email' => $email,
			]
		);
	}
}
