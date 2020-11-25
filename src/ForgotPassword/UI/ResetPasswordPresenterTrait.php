<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\UI;

use Nette\InvalidStateException;
use Nette\Application\BadRequestException;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException;
use SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControl;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface;
use SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControlFactoryInterface;

trait ResetPasswordPresenterTrait
{
	/** @var NULL|\SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface */
	private $passwordRequestManager;

	/** @var NULL|\SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControlFactoryInterface */
	public $resetPasswordControlFactory;

	/** @var NULL|\SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface */
	private $passwordRequest;

	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface $passwordRequestManager
	 *
	 * @return void
	 */
	public function injectPasswordRequestManager(PasswordRequestManagerInterface $passwordRequestManager): void
	{
		$this->passwordRequestManager = $passwordRequestManager;
	}

	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControlFactoryInterface $resetPasswordControlFactory
	 *
	 * @return void
	 */
	public function injectResetPasswordControlFactory(ResetPasswordControlFactoryInterface $resetPasswordControlFactory): void
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
		} catch (PasswordRequestProcessException $e) {
			$this->triggerPasswordRequestNotFound($e);

			throw new BadRequestException();
		}
	}

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e
	 *
	 * @return void
	 */
	abstract protected function triggerPasswordRequestNotFound(PasswordRequestProcessException $e): void;

	/**
	 * @return \SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControl
	 */
	protected function createResetPasswordControl(): ResetPasswordControl
	{
		if (!$this->passwordRequest instanceof PasswordRequestInterface) {
			throw new InvalidStateException('Password Request is not set.');
		}

		return $this->resetPasswordControlFactory->create($this->passwordRequest);
	}
}
