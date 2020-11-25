<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use Nette\SmartObject;
use SixtyEightPublishers\User\Common\Mail\Address;
use SixtyEightPublishers\User\Common\Mail\MailSenderInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;

final class ForgotPasswordResetEmail implements ForgotPasswordResetEmailInterface
{
	use SmartObject;

	public const NAME = 'forgot_password_reset_email';

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
	public function send(PasswordRequestInterface $request): void
	{
		$this->mailSender->send(
			self::NAME,
			[
				new Address($request->getUser()->getEmail()),
			],
			[
				'request' => $request,
			]
		);
	}
}
