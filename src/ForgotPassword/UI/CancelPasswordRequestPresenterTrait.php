<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\UI;

use Nette\Application\BadRequestException;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface;

/**
 * Use in Presenter!
 *
 * @property-read string $action
 * @method void changeAction($action)
 */
trait CancelPasswordRequestPresenterTrait
{
	/** @var NULL|\SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface */
	private $passwordRequestManager;

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
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionDefault(string $uid, string $rid): void
	{
		try {
			$this->passwordRequestManager->cancel(
				$request = $this->passwordRequestManager->findRequest($uid, $rid)
			);

			$this->triggerSuccess($request);
		} catch (PasswordRequestProcessException $e) {
			$this->triggerError($e);

			throw new BadRequestException();
		}
	}

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface $passwordRequest
	 *
	 * @return void
	 */
	abstract protected function triggerSuccess(PasswordRequestInterface $passwordRequest): void;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e
	 *
	 * @return void
	 */
	abstract protected function triggerError(PasswordRequestProcessException $e): void;
}
