<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use Throwable;
use Nette\SmartObject;
use SixtyEightPublishers\User\Common\Logger\LoggerInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordResetEmailInterface;
use SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException;
use SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordNotRegisteredEmailInterface;

class PasswordRequestSender implements PasswordRequestSenderInterface
{
	use SmartObject;

	/** @var \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestFactoryInterface  */
	private $passwordRequestFactory;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordResetEmailInterface  */
	private $forgotPasswordResetEmail;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordNotRegisteredEmailInterface  */
	private $forgotPasswordNotRegisteredEmail;

	/** @var \SixtyEightPublishers\User\Common\Logger\LoggerInterface  */
	protected $logger;

	/** @var bool */
	protected $sendEmailForNotRegisteredUsers;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestFactoryInterface $passwordRequestFactory
	 * @param \SixtyEightPublishers\User\Common\Logger\LoggerInterface                                  $logger
	 * @param \SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordResetEmailInterface          $forgotPasswordResetEmail
	 * @param \SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordNotRegisteredEmailInterface  $forgotPasswordNotRegisteredEmail
	 * @param bool                                                                                      $sendEmailForNotRegisteredUsers
	 */
	public function __construct(PasswordRequestFactoryInterface $passwordRequestFactory, LoggerInterface $logger, ForgotPasswordResetEmailInterface $forgotPasswordResetEmail, ForgotPasswordNotRegisteredEmailInterface $forgotPasswordNotRegisteredEmail, bool $sendEmailForNotRegisteredUsers = TRUE)
	{
		$this->passwordRequestFactory = $passwordRequestFactory;
		$this->logger = $logger;
		$this->forgotPasswordResetEmail = $forgotPasswordResetEmail;
		$this->forgotPasswordNotRegisteredEmail = $forgotPasswordNotRegisteredEmail;
		$this->sendEmailForNotRegisteredUsers = $sendEmailForNotRegisteredUsers;
	}

	/**
	 * @param string $email
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface
	 */
	public function send(string $email): ?PasswordRequestInterface
	{
		try {
			$this->sendForgotPasswordResetEmail(
				$request = $this->passwordRequestFactory->create($email)
			);
		} catch (PasswordRequestCreationException $e) {
			if (!$e->isNotRegisteredEmail()) {
				$this->logError($e);

				throw $e;
			}

			if (!$this->sendEmailForNotRegisteredUsers) {
				throw $e;
			}

			try {
				$this->sendForgotPasswordNotRegisteredEmail($email);
			} catch (Throwable $e) {
				$this->logError($e);

				throw PasswordRequestCreationException::from($e);
			}
		} catch (Throwable $e) {
			throw PasswordRequestCreationException::from($e);
		}

		return $request ?? NULL;
	}

	/**
	 * @param string $email
	 * @param string $mailClassName
	 *
	 * @return void
	 */
	protected function logSentEmail(string $email, string $mailClassName): void
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
	protected function logError(Throwable $e): void
	{
		$this->logger->error((string) $e);
	}

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface $request
	 *
	 * @return void
	 */
	protected function sendForgotPasswordResetEmail(PasswordRequestInterface $request): void
	{
		$this->forgotPasswordResetEmail->send($request);
		$this->logSentEmail($request->getUser()->getEmail(), get_class($this->forgotPasswordResetEmail));
	}

	/**
	 * @param string $email
	 *
	 * @return void
	 */
	protected function sendForgotPasswordNotRegisteredEmail(string $email): void
	{
		$this->forgotPasswordNotRegisteredEmail->send($email);
		$this->logSentEmail($email, get_class($this->forgotPasswordNotRegisteredEmail));
	}
}
