<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Control\SignIn;

use Nette\Security\User;
use Nette\Application\UI\Form;
use Nette\Security\IAuthenticator;
use Nette\Security\AuthenticationException;
use SixtyEightPublishers\SmartNetteComponent\UI\Control;
use SixtyEightPublishers\User\Common\Logger\LoggerInterface;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\User\Authentication\Authenticator\AuthenticatorMount;

/**
 * @method void onLoggedIn()
 * @method void onAuthenticationFail(AuthenticationException $e)
 * @method void onFormCreation(Form $form)
 */
final class SignInControl extends Control implements TranslatorAwareInterface
{
	use TranslatorAwareTrait;

	/** @var \Nette\Security\User  */
	private $user;

	/** @var \SixtyEightPublishers\User\Common\Logger\LoggerInterface  */
	private $logger;

	/** @var NULL|string */
	private $usernamePrefix;

	/** @var string|int|\DateTimeInterface */
	private $expiration;

	/** @var callable[]  */
	public $onLoggedIn = [];

	/** @var callable[]  */
	public $onAuthenticationFail = [];

	/** @var callable[]  */
	public $onFormCreation = [];

	/**
	 * @param \Nette\Security\User                                     $user
	 * @param \SixtyEightPublishers\User\Common\Logger\LoggerInterface $logger
	 */
	public function __construct(User $user, LoggerInterface $logger)
	{
		$this->user = $user;
		$this->logger = $logger;
	}

	/**
	 * Use with AuthenticatorMount
	 *
	 * @param string $usernamePrefix
	 *
	 * @return void
	 */
	public function setUsernamePrefix(string $usernamePrefix): void
	{
		$this->usernamePrefix = $usernamePrefix;
	}

	/**
	 * @param \DateTimeInterface|int|string $expiration
	 *
	 * @return void
	 */
	public function setExpiration($expiration): void
	{
		$this->expiration = $expiration;
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

		$form->addText('username', 'username.field')
			->setRequired('username.required')
			->setHtmlAttribute('autocomplete', 'username');

		$form->addPassword('password', 'password.field')
			->setRequired('password.required')
			->setHtmlAttribute('autocomplete', 'current-password');

		$form->addProtection('protection.rule');

		$form->onSuccess[] = [$this, 'processForm'];

		$this->onFormCreation($form);

		$form->addSubmit('login', 'login.field');

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
			$username = $form->values->username;
			$password = $form->values->password;

			if (!empty($this->usernamePrefix)) {
				$username = $this->usernamePrefix . AuthenticatorMount::SEPARATOR . $username;
			}

			if (NULL !== $this->expiration) {
				$this->user->setExpiration($this->expiration);
			}

			$this->user->login($username, $password);
			$this->onLoggedIn();
		} catch (AuthenticationException $e) {
			if (IAuthenticator::FAILURE === $e->getCode()) {
				$this->logger->error((string) $e);
			}

			$this->onAuthenticationFail($e);
		}
	}
}
