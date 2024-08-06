<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use Nette;
use SixtyEightPublishers;

final class PasswordHasBeenResetEmail implements PasswordHasBeenResetEmailInterface
{
	use Nette\SmartObject;

	public const NAME = 'password_has_been_reset_email';

	/** @var \SixtyEightPublishers\User\Common\Mail\MailSenderInterface  */
	private $mailSender;

	/**
	 * @param \SixtyEightPublishers\User\Common\Mail\MailSenderInterface $mailSender
	 */
	public function __construct(SixtyEightPublishers\User\Common\Mail\MailSenderInterface $mailSender)
	{
		$this->mailSender = $mailSender;
	}

	/**
	 * {@inheritdoc}
	 */
	public function send(SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface $user): void
	{
		$this->mailSender->send(
			self::NAME,
			[
				new SixtyEightPublishers\User\Common\Mail\Address($user->getEmail()),
			],
			[
				'userEntity' => $user,
			]
		);
	}
}
