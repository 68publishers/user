<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword;

use Throwable;
use Nette\Application\UI\Form;
use SixtyEightPublishers\SmartNetteComponent\UI\Control;
use SixtyEightPublishers\User\Common\Logger\LoggerInterface;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\User\ForgotPassword\Mail\PasswordHasBeenResetEmailInterface;
use SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface;

/**
 * @method void onSuccess(PasswordRequestInterface $request, string $rawPassword)
 * @method void onError(PasswordRequestInterface $request, PasswordRequestProcessException $e)
 * @method void onFormCreation(Form $form)
 */
class ResetPasswordControl extends Control implements TranslatorAwareInterface
{
	use TranslatorAwareTrait;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface  */
	private $passwordRequest;

	/** @var \SixtyEightPublishers\User\Common\Logger\LoggerInterface  */
	private $logger;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Mail\PasswordHasBeenResetEmailInterface  */
	private $passwordHasBeenResetEmail;

	/** @var \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface  */
	private $passwordRequestManager;

	/** @var callable[] */
	public $onSuccess = [];

	/** @var callable[] */
	public $onError = [];

	/** @var callable[] */
	public $onFormCreation = [];

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface                 $passwordRequest
	 * @param \SixtyEightPublishers\User\Common\Logger\LoggerInterface                                  $logger
	 * @param \SixtyEightPublishers\User\ForgotPassword\Mail\PasswordHasBeenResetEmailInterface         $passwordHasBeenResetEmail
	 * @param \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface $passwordRequestManager
	 */
	public function __construct(PasswordRequestInterface $passwordRequest, LoggerInterface $logger, PasswordHasBeenResetEmailInterface $passwordHasBeenResetEmail, PasswordRequestManagerInterface $passwordRequestManager)
	{
		$this->passwordRequest = $passwordRequest;
		$this->logger = $logger;
		$this->passwordHasBeenResetEmail = $passwordHasBeenResetEmail;
		$this->passwordRequestManager = $passwordRequestManager;
	}

	/**
	 * @return void
	 */
	public function render(): void
	{
		$this->template->setTranslator($this->getPrefixedTranslator());
		$this->doRender();
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = new Form();

		$form->setTranslator($this->getPrefixedTranslator());

		$form->addPassword('password', 'password.field')
			->setRequired('password.required')
			->setHtmlAttribute('autocomplete', 'new-password');

		$form->addProtection('protection.rule');

		$form->onSuccess[] = [$this, 'processForm'];

		$this->onFormCreation($form);

		$form->addSubmit('send', 'send.field');

		return $form;
	}

	/**
	 * @internal
	 *
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	public function processForm(Form $form): void
	{
		try {
			$password = $form->values->password;

			$this->passwordRequestManager->reset($this->passwordRequest, $password);

			try {
				$this->passwordHasBeenResetEmail->send($this->passwordRequest->getUser());
				$this->logger->info(sprintf(
					'Mail %s was successfully sent to %s',
					get_class($this->passwordHasBeenResetEmail),
					$this->passwordRequest->getUser()->getEmail()
				));
			} catch (Throwable $e) {
				$this->logger->error((string) $e);
			}

			$this->onSuccess($this->passwordRequest, $password);
		} catch (PasswordRequestProcessException $e) {
			$this->onError($this->passwordRequest, $e);
		}
	}
}
