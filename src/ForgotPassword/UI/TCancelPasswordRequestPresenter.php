<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\UI;

use Nette;
use SixtyEightPublishers;

/**
 * Use in Presenter!
 *
 * @property-read string $action
 * @method void changeAction($action)
 */
trait TCancelPasswordRequestPresenter
{
	/** @var NULL|\SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestManager */
	private $passwordRequestManager;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest
	 *
	 * @return void
	 */
	abstract protected function triggerSuccess(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest): void;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e
	 *
	 * @return void
	 */
	abstract protected function triggerError(SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e): void;

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
	 * {@inheritdoc}
	 */
	public function startup(): void
	{
		/** @noinspection PhpUndefinedClassInspection */
		parent::startup();

		if ($this->action !== 'default') {
			$this->changeAction('default');
		}
	}

	/**
	 * @param string $uid User UUID
	 * @param string $rid PasswordRequest UUID
	 *
	 * @return void
	 */
	public function actionDefault(string $uid, string $rid): void
	{
		try {
			$this->passwordRequestManager->cancel(
				$request = $this->passwordRequestManager->findRequest($uid, $rid)
			);

			$this->triggerSuccess($request);
		} catch (SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e) {
			$this->triggerError($e);

			throw new Nette\Application\BadRequestException();
		}
	}
}
