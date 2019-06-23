<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Control\ForgotPassword;

use Nette;
use SixtyEightPublishers;

/**
 * @method void onSend(string $email, ?SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest)
 * @method void onError(SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException $e, string $email)
 * @method void onFormCreation(Nette\Application\UI\Form $form)
 */
class ForgotPasswordControl extends SixtyEightPublishers\SmartNetteComponent\UI\Control
{
	/** @var \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestSender  */
	private $passwordRequestSender;

	/** @var callable[] */
	public $onSend = [];

	/** @var callable[] */
	public $onError = [];

	/** @var callable[] */
	public $onFormCreation = [];

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestSender $passwordRequestSender
	 */
	public function __construct(SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestSender $passwordRequestSender)
	{
		parent::__construct();

		$this->passwordRequestSender = $passwordRequestSender;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm() : Nette\Application\UI\Form
	{
		$form = new Nette\Application\UI\Form();

		$form->addText('email', 'Email')
			->setRequired('Please paste your email.')
			->addRule($form::EMAIL)
			->setAttribute('autocomplete', 'username');

		$form->addSubmit('send', 'Send');
		$form->addProtection();

		$form->onSuccess[] = [ $this, 'processForm' ];

		$this->onFormCreation($form);

		return $form;
	}

	/**
	 * @return void
	 */
	public function render() : void
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
	public function processForm(Nette\Application\UI\Form $form) : void
	{
		$email = $form->values->email;

		try {
			$request = $this->passwordRequestSender->send($email);

			$this->onSend($email, $request);
		} catch (SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException $e) {
			$this->onError($e, $email);
		}
	}
}
