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
class ForgotPasswordControl extends SixtyEightPublishers\SmartNetteComponent\UI\Control implements SixtyEightPublishers\User\Common\Translator\ITranslatableService
{
	use SixtyEightPublishers\User\Common\Translator\TTranslatableService;

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
	protected function createComponentForm(): Nette\Application\UI\Form
	{
		$form = new Nette\Application\UI\Form();

		$form->setTranslator(SixtyEightPublishers\User\Common\Translator\PrefixedTranslator::createFromClassName(
			$this->getTranslator(),
			static::class
		));

		$form->addText('email', 'email.field')
			->setRequired('email.required')
			->addRule($form::EMAIL, 'email.rule')
			->setAttribute('autocomplete', 'username');

		$form->addProtection('protection.rule');

		$form->onSuccess[] = [ $this, 'processForm' ];

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
		$email = $form->values->email;

		try {
			$request = $this->passwordRequestSender->send($email);

			$this->onSend($email, $request);
		} catch (SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException $e) {
			$this->onError($e, $email);
		}
	}
}
