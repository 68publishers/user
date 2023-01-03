<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Control\ForgotPassword;

use Nette\Application\UI\Form;
use Nette\Application\UI\Control;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestSenderInterface;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\TemplateResolverTrait;

/**
 * @method void onSend(string $email, ?PasswordRequestInterface $passwordRequest)
 * @method void onError(PasswordRequestCreationException $e, string $email)
 * @method void onFormCreation(Form $form)
 */
class ForgotPasswordControl extends Control implements TranslatorAwareInterface
{
	use TranslatorAwareTrait;
	use TemplateResolverTrait;

	/** @var \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestSenderInterface  */
	private $passwordRequestSender;
	
	/** @var callable[] */
	public $onSend = [];

	/** @var callable[] */
	public $onError = [];

	/** @var callable[] */
	public $onFormCreation = [];

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestSenderInterface $passwordRequestSender
	 */
	public function __construct(PasswordRequestSenderInterface $passwordRequestSender)
	{
		$this->passwordRequestSender = $passwordRequestSender;
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

		$form->addText('email', 'email.field')
			->setRequired('email.required')
			->addRule($form::EMAIL, 'email.rule')
			->setHtmlAttribute('autocomplete', 'username');

		$form->addProtection('protection.rule');

		$form->onSuccess[] = [ $this, 'processForm' ];

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
		$email = $form->values->email;

		try {
			$request = $this->passwordRequestSender->send($email);

			$this->onSend($email, $request);
		} catch (PasswordRequestCreationException $e) {
			$this->onError($e, $email);
		}
	}
}
