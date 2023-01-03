<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword;

use Throwable;
use Nette\Security\User;
use Nette\Application\UI\Form;
use Nette\Application\UI\Control;
use SixtyEightPublishers\User\Common\Logger\LoggerInterface;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\User\ForgotPassword\Mail\PasswordHasBeenResetEmailInterface;
use SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\TemplateResolverTrait;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface;

/**
 * @method void onSuccess(PasswordRequestInterface $request)
 * @method void onError(PasswordRequestInterface $request, PasswordRequestProcessException $e)
 * @method void onFormCreation(Form $form)
 */
class ResetPasswordControl extends Control implements TranslatorAwareInterface
{
	use TranslatorAwareTrait;
	use TemplateResolverTrait;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface  */
	private $passwordRequest;

	/** @var \SixtyEightPublishers\User\Common\Logger\LoggerInterface  */
	private $logger;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Mail\PasswordHasBeenResetEmailInterface  */
	private $passwordHasBeenResetEmail;

	/** @var \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface  */
	private $passwordRequestManager;

	/** @var \Nette\Security\User  */
	private $user;

	/** @var bool  */
	private $autoLogin = FALSE;

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
	 * @param \Nette\Security\User                                                                      $user
	 */
	public function __construct(
		PasswordRequestInterface $passwordRequest,
		LoggerInterface $logger,
		PasswordHasBeenResetEmailInterface $passwordHasBeenResetEmail,
		PasswordRequestManagerInterface $passwordRequestManager,
		User $user
	) {
		$this->passwordRequest = $passwordRequest;
		$this->logger = $logger;
		$this->passwordHasBeenResetEmail = $passwordHasBeenResetEmail;
		$this->passwordRequestManager = $passwordRequestManager;
		$this->user = $user;
	}

	/**
	 * @param bool $autoLogin
	 *
	 * @return void
	 */
	public function setAutoLogin(bool $autoLogin): void
	{
		$this->autoLogin = $autoLogin;
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
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function processForm(Form $form): void
	{
		try {
			$request = $this->passwordRequest;
			$password = $form->values->password;

			$this->passwordRequestManager->reset($request, $password);

			try {
				$this->passwordHasBeenResetEmail->send($request->getUser());
				$this->logger->info(sprintf(
					'Mail %s was successfully sent to %s',
					get_class($this->passwordHasBeenResetEmail),
					$request->getUser()->getEmail()
				));
			} catch (Throwable $e) {
				$this->logger->error((string) $e);
			}

			if ($this->autoLogin) {
				$this->user->login($request->getUser()->getUsername(), $password);
			}

			$this->onSuccess($this->passwordRequest);
		} catch (PasswordRequestProcessException $e) {
			$this->onError($this->passwordRequest, $e);
		}
	}
}
