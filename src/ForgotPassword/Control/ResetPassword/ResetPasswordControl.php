<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword;

use Nette;
use SixtyEightPublishers;

/**
 * @method void onSuccess(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request, string $rawPassword)
 * @method void onError(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request, SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e)
 * @method void onFormCreation(Nette\Application\UI\Form $form)
 */
class ResetPasswordControl extends SixtyEightPublishers\SmartNetteComponent\UI\Control implements SixtyEightPublishers\User\Common\Translator\ITranslatorAware
{
	use SixtyEightPublishers\User\Common\Translator\TTranslatorAware;

	/** @var \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest  */
	private $passwordRequest;

	/** @var \SixtyEightPublishers\User\Common\Logger\ILogger  */
	private $logger;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Mail\IPasswordHasBeenResetEmail  */
	private $passwordHasBeenResetEmail;

	/** @var \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestManager  */
	private $passwordRequestManager;

	/** @var callable[] */
	public $onSuccess = [];

	/** @var callable[] */
	public $onError = [];

	/** @var callable[] */
	public $onFormCreation = [];

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest         $passwordRequest
	 * @param \SixtyEightPublishers\User\Common\Logger\ILogger                                  $logger
	 * @param \SixtyEightPublishers\User\ForgotPassword\Mail\IPasswordHasBeenResetEmail         $passwordHasBeenResetEmail
	 * @param \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestManager $passwordRequestManager
	 */
	public function __construct(
		SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest,
		SixtyEightPublishers\User\Common\Logger\ILogger $logger,
		SixtyEightPublishers\User\ForgotPassword\Mail\IPasswordHasBeenResetEmail $passwordHasBeenResetEmail,
		SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestManager $passwordRequestManager
	) {
		parent::__construct();

		$this->passwordRequest = $passwordRequest;
		$this->logger = $logger;
		$this->passwordHasBeenResetEmail = $passwordHasBeenResetEmail;
		$this->passwordRequestManager = $passwordRequestManager;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Nette\Application\UI\Form
	{
		$form = new Nette\Application\UI\Form();

		$form->setTranslator(SixtyEightPublishers\User\Common\Translator\PrefixedTranslator::createFromClassName(
			$this->getTranslator(),
			static::class
		));

		$form->addPassword('password', 'password.field')
			->setRequired('password.required')
			->setAttribute('autocomplete', 'new-password');

		$form->addProtection('protection.rule');

		$form->onSuccess[] = [$this, 'processForm'];

		$this->onFormCreation($form);

		$form->addSubmit('send', 'send.field');

		return $form;
	}

	/**
	 * @return void
	 */
	public function render(): void
	{
		$this->doRender();
	}

	/**
	 * @internal
	 *
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	public function processForm(Nette\Application\UI\Form $form): void
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
			} catch (\Throwable $e) {
				$this->logger->error((string) $e);
			}

			$this->onSuccess($this->passwordRequest, $password);
		} catch (SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException $e) {
			$this->onError($this->passwordRequest, $e);
		}
	}
}
