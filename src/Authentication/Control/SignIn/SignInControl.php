<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Control\SignIn;

use Nette;
use SixtyEightPublishers;

/**
 * @method void onLoggedIn()
 * @method void onAuthenticationFail(Nette\Security\AuthenticationException $e)
 * @method void onFormCreation(Nette\Application\UI\Form $form)
 */
final class SignInControl extends SixtyEightPublishers\SmartNetteComponent\UI\Control implements SixtyEightPublishers\User\Common\Translator\ITranslatorAware
{
	use SixtyEightPublishers\User\Common\Translator\TTranslatorAware;

	/** @var \Nette\Security\User  */
	private $user;

	/** @var \SixtyEightPublishers\User\Common\Logger\ILogger  */
	private $logger;

	/** @var NULL|string */
	private $usernamePrefix;

	/** @var callable[]  */
	public $onLoggedIn = [];

	/** @var callable[]  */
	public $onAuthenticationFail = [];

	/** @var callable[]  */
	public $onFormCreation = [];

	/**
	 * @param \Nette\Security\User                             $user
	 * @param \SixtyEightPublishers\User\Common\Logger\ILogger $logger
	 */
	public function __construct(Nette\Security\User $user, SixtyEightPublishers\User\Common\Logger\ILogger $logger)
	{
		parent::__construct();

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
	 * @return void
	 */
	public function render(): void
	{
		$this->doRender();
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

		$form->addText('username', 'username.field')
			->setRequired('username.required')
			->setAttribute('autocomplete', 'username');

		$form->addPassword('password', 'password.field')
			->setRequired('password.required')
			->setAttribute('autocomplete', 'current-password');

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
	public function processForm(Nette\Application\UI\Form $form): void
	{
		try {
			$username = $form->values->username;
			$password = $form->values->password;

			if (!empty($this->usernamePrefix)) {
				$username = $this->usernamePrefix . SixtyEightPublishers\User\Authentication\Authenticator\AuthenticatorMount::SEPARATOR . $username;
			}

			$this->user->login($username, $password);
			$this->onLoggedIn();
		} catch (Nette\Security\AuthenticationException $e) {
			if (Nette\Security\IAuthenticator::FAILURE === $e->getCode()) {
				$this->logger->error((string) $e);
			}

			$this->onAuthenticationFail($e);
		}
	}
}
