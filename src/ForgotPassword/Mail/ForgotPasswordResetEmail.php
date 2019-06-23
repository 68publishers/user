<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Mail;

use Nette;
use SixtyEightPublishers;

final class ForgotPasswordResetEmail implements IForgotPasswordResetEmail
{
	use Nette\SmartObject;

	const NAME = 'forgot_password_reset_email';

	/** @var \SixtyEightPublishers\User\Common\Mail\IMailSender  */
	private $mailSender;

	/**
	 * @param \SixtyEightPublishers\User\Common\Mail\IMailSender $mailSender
	 */
	public function __construct(SixtyEightPublishers\User\Common\Mail\IMailSender $mailSender)
	{
		$this->mailSender = $mailSender;
	}

	/******** interface \SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordResetEmail ********/

	/**
	 * {@inheritdoc}
	 */
	public function send(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request) : void
	{
		$this->mailSender->send(
			self::NAME,
			[
				new SixtyEightPublishers\User\Common\Mail\Address($request->getUser()->getEmail()),
			],
			[
				'request' => $request,
			]
		);
	}
}
