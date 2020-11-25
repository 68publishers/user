<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use Nette\SmartObject;
use SixtyEightPublishers\User\Common\Mail\Address;
use SixtyEightPublishers\User\Common\Mail\MailSenderInterface;

final class ForgotPasswordNotRegisteredEmail implements ForgotPasswordNotRegisteredEmailInterface
{
	use SmartObject;

	public const NAME = 'forgot_password_not_registered_email';

	/** @var \SixtyEightPublishers\User\Common\Mail\MailSenderInterface  */
	private $mailSender;

	/**
	 * @param \SixtyEightPublishers\User\Common\Mail\MailSenderInterface $mailSender
	 */
	public function __construct(MailSenderInterface $mailSender)
	{
		$this->mailSender = $mailSender;
	}

	/**
	 * {@inheritdoc}
	 */
	public function send(string $email): void
	{
		$this->mailSender->send(
			self::NAME,
			[
				new Address($email),
			],
			[
				'email' => $email,
			]
		);
	}
}
