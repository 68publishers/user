<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use Nette;
use SixtyEightPublishers;

class PasswordRequestSender implements IPasswordRequestSender
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestFactory  */
	private $passwordRequestFactory;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordResetEmail  */
	private $forgotPasswordResetEmail;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordNotRegisteredEmail  */
	private $forgotPasswordNotRegisteredEmail;

	/** @var \SixtyEightPublishers\User\Common\Logger\ILogger  */
	protected $logger;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestFactory $passwordRequestFactory
	 * @param \SixtyEightPublishers\User\Common\Logger\ILogger                                  $logger
	 * @param \SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordResetEmail          $forgotPasswordResetEmail
	 * @param \SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordNotRegisteredEmail  $forgotPasswordNotRegisteredEmail
	 */
	public function __construct(
		IPasswordRequestFactory $passwordRequestFactory,
		SixtyEightPublishers\User\Common\Logger\ILogger $logger,
		SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordResetEmail $forgotPasswordResetEmail,
		SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordNotRegisteredEmail $forgotPasswordNotRegisteredEmail
	) {
		$this->passwordRequestFactory = $passwordRequestFactory;
		$this->logger = $logger;
		$this->forgotPasswordResetEmail = $forgotPasswordResetEmail;
		$this->forgotPasswordNotRegisteredEmail = $forgotPasswordNotRegisteredEmail;
	}

	/**
	 * @param string $email
	 * @param string $mailClassName
	 *
	 * @return void
	 */
	protected function logSentEmail(string $email, string $mailClassName) : void
	{
		$this->logger->info(sprintf(
			'Mail %s was successfully sent to %s',
			$mailClassName,
			$email
		));
	}

	/**
	 * @param \Throwable $e
	 *
	 * @return void
	 */
	protected function logError(\Throwable $e) : void
	{
		$this->logger->error((string) $e);
	}

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request
	 *
	 * @return void
	 */
	protected function sendForgotPasswordResetEmail(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request) : void
	{
		$this->forgotPasswordResetEmail->send($request);
		$this->logSentEmail($request->getUser()->getEmail(), get_class($this->forgotPasswordResetEmail));
	}

	/**
	 * @param string $email
	 *
	 * @return void
	 */
	protected function sendForgotPasswordNotRegisteredEmail(string $email) : void
	{
		$this->forgotPasswordNotRegisteredEmail->send($email);
		$this->logSentEmail($email, get_class($this->forgotPasswordNotRegisteredEmail));
	}

	/*********** interface \SixtyEightPublishers\User\ForgotPassword\IPasswordRequestSender ***********/

	/**
	 * @param string $email
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest
	 */
	public function send(string $email) : ?SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest
	{
		try {
			$this->forgotPasswordResetEmail->send(
				$request = $this->passwordRequestFactory->create($email)
			);
		} catch (SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException $e) {
			if (!$e->isNotRegisteredEmail()) {
				$this->logError($e);

				throw $e;
			}

			try {
				$this->forgotPasswordNotRegisteredEmail->send($email);
			} catch (\Throwable $e) {
				$this->logError($e);

				throw SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException::from($e);
			}
		} catch (\Throwable $e) {
			throw SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException::from($e);
		}

		return $request ?? NULL;
	}
}
