<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\UI;

use Nette;
use SixtyEightPublishers;

trait TResetPasswordPresenter
{
	/** @var NULL|\SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestManager */
	private $passwordRequestManager;

	/** @var NULL|\SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\IResetPasswordControlFactory */
	public $resetPasswordControlFactory;

	/** @var NULL|\SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest */
	private $passwordRequest;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e
	 *
	 * @return void
	 */
	abstract protected function triggerPasswordRequestNotFound(SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e): void;

	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestManager $passwordRequestManager
	 *
	 * @return void
	 */
	public function injectPasswordRequestManager(SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestManager $passwordRequestManager): void
	{
		$this->passwordRequestManager = $passwordRequestManager;
	}

	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\IResetPasswordControlFactory $resetPasswordControlFactory
	 *
	 * @return void
	 */
	public function injectResetPasswordControlFactory(SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\IResetPasswordControlFactory $resetPasswordControlFactory): void
	{
		$this->resetPasswordControlFactory = $resetPasswordControlFactory;
	}

	/**
	 * @param string $uid User UUID
	 * @param string $rid PasswordRequest UUID
	 *
	 * @return void
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionDefault(string $uid, string $rid): void
	{
		try {
			$this->passwordRequest = $this->passwordRequestManager->findRequest($uid, $rid);
		} catch (SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e) {
			$this->triggerPasswordRequestNotFound($e);

			throw new Nette\Application\BadRequestException();
		}
	}

	/**
	 * @return \SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControl
	 */
	protected function createResetPasswordControl(): SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControl
	{
		if (!$this->passwordRequest instanceof SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest) {
			throw new Nette\InvalidStateException('Password Request is not set.');
		}

		return $this->resetPasswordControlFactory->create($this->passwordRequest);
	}
}
