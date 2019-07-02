<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\DI;

use Kdyby;
use Nette;
use SixtyEightPublishers;

final class AuthenticationExtensionAdapter extends SixtyEightPublishers\User\DI\AbstractExtensionAdapter implements
	Kdyby\Doctrine\DI\ITargetEntityProvider,
	Kdyby\Translation\DI\ITranslationProvider
{
	/** @var array  */
	protected static $defaults = [
		'enabled' => FALSE,
		'authenticator' => SixtyEightPublishers\User\Authentication\Authenticator\Authenticator::class,
		'csrf_token_factory' => SixtyEightPublishers\User\Authentication\Csrf\CsrfTokenFactory::class,
		'register_controls' => FALSE,
	];

	/** @var NULL|string */
	private $authenticatorName;

	/**
	 * {@inheritdoc}
	 */
	protected function processConfig(array $config, \ArrayObject $sharedData): array
	{
		Nette\Utils\Validators::assertField($config, 'enabled', 'bool');
		Nette\Utils\Validators::assertField($config, 'authenticator', 'string|' . Nette\DI\Statement::class);
		Nette\Utils\Validators::assertField($config, 'csrf_token_factory', 'string|' . Nette\DI\Statement::class);
		Nette\Utils\Validators::assertField($config, 'register_controls', 'bool');

		if (FALSE === $config['enabled']) {
			$this->stopPropagation();
		}

		if (!is_subclass_of(
			$sharedData[SixtyEightPublishers\User\Common\DI\CommonExtensionAdapter::SHARED_DATA_USER_CLASS_NAME],
			SixtyEightPublishers\User\Authentication\DoctrineEntity\IUser::class,
			TRUE
		)
		) {
			throw new SixtyEightPublishers\User\Common\Exception\ConfigurationException(sprintf(
				'Your User entity must implement interface %s',
				SixtyEightPublishers\User\Authentication\DoctrineEntity\IUser::class
			));
		}

		return $config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();
		$authenticator = $config['authenticator'];
		$csrfTokenFactory = $config['csrf_token_factory'];

		# authenticator
		if (!is_string($authenticator) || !Nette\Utils\Strings::startsWith($authenticator, '@')) {
			$builder->addDefinition($this->prefix('authenticator'))
				->setType(Nette\Security\IAuthenticator::class)
				->setFactory($authenticator);

			$this->authenticatorName = $this->prefix('authenticator');
		} else {
			$this->authenticatorName = Nette\Utils\Strings::substring($authenticator, 1);
		}

		if (isset($builder->getAliases()['nette.authenticator'])) {
			$builder->removeAlias('nette.authenticator');
		}

		$builder->addAlias('nette.authenticator', $this->authenticatorName);

		# CSRF
		if (!is_string($csrfTokenFactory) || !Nette\Utils\Strings::startsWith($csrfTokenFactory, '@')) {
			$builder->addDefinition($this->prefix('csrf_token_factory'))
				->setType(SixtyEightPublishers\User\Authentication\Csrf\ICsrfTokenFactory::class)
				->setFactory($csrfTokenFactory);
		}

		# controls
		if (TRUE === $config['register_controls']) {
			$builder->addDefinition($this->prefix('control.sign_in'))
				->setImplement(SixtyEightPublishers\User\Authentication\Control\SignIn\ISignInControlFactory::class)
				->setFactory(SixtyEightPublishers\User\Authentication\Control\SignIn\SignInControl::class);
		}

		# queries
		$builder->addDefinition($this->prefix('query_factory.authenticator'))
			->setType(SixtyEightPublishers\User\Authentication\Query\IAuthenticatorQueryFactory::class)
			->setFactory(SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryFactory::class);
	}

	/**************** interface \Kdyby\Doctrine\DI\ITargetEntityProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getTargetEntityMappings(): array
	{
		return [
			SixtyEightPublishers\User\Authentication\DoctrineEntity\IUser::class => $this->getSharedData(SixtyEightPublishers\User\Common\DI\CommonExtensionAdapter::SHARED_DATA_USER_CLASS_NAME),
		];
	}

	/**************** interface \Kdyby\Translation\DI\ITranslationProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getTranslationResources(): array
	{
		$config = $this->getConfig();

		return TRUE === $config['register_controls']
			? [ __DIR__ . '/../locale' ]
			: [];
	}
}
