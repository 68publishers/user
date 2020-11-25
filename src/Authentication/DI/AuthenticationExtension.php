<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\DI;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Security\IAuthenticator;
use Nette\DI\Definitions\Statement;
use SixtyEightPublishers\DoctrineBridge\DI\TargetEntity;
use SixtyEightPublishers\User\Common\DI\CommonExtension;
use SixtyEightPublishers\User\DI\AbstractCompilerExtensionPass;
use SixtyEightPublishers\User\Authentication\Entity\UserInterface;
use SixtyEightPublishers\User\Authentication\Csrf\CsrfTokenFactory;
use SixtyEightPublishers\User\Common\Exception\ConfigurationException;
use SixtyEightPublishers\DoctrineBridge\DI\TargetEntityProviderInterface;
use SixtyEightPublishers\User\Authentication\Authenticator\Authenticator;
use SixtyEightPublishers\User\Authentication\Control\SignIn\SignInControl;
use SixtyEightPublishers\TranslationBridge\DI\TranslationProviderInterface;
use SixtyEightPublishers\User\Authentication\Csrf\CsrfTokenFactoryInterface;
use SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryObject;
use SixtyEightPublishers\User\Authentication\Control\SignIn\SignInControlFactoryInterface;
use SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryObjectFactoryInterface;

final class AuthenticationExtension extends AbstractCompilerExtensionPass implements TargetEntityProviderInterface, TranslationProviderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function startup(): void
	{
		parent::startup();

		if (!$this->config->enabled) {
			$this->stopPropagation();
		}

		if (!is_subclass_of($this->sharedData[CommonExtension::SHARED_DATA_USER_CLASS_NAME], UserInterface::class, TRUE)) {
			throw new ConfigurationException(sprintf(
				'Your User entity must implement interface %s',
				UserInterface::class
			));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'enabled' => Expect::bool(FALSE),
			'authenticator' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))
				->default(Authenticator::class)
				->before(static function ($def) {
					return $def instanceof Statement ? $def : new Statement($def);
				}),
			'csrf_token_factory' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))
				->default(CsrfTokenFactory::class)
				->before(static function ($def) {
					return $def instanceof Statement ? $def : new Statement($def);
				}),
			'register_controls' => Expect::bool(FALSE),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('authenticator'))
			->setType(IAuthenticator::class)
			->setFactory($this->config->authenticator);

		if (isset($builder->getAliases()['nette.authenticator'])) {
			$builder->removeAlias('nette.authenticator');
		}

		$builder->addAlias('nette.authenticator', $this->prefix('authenticator'));

		$builder->addDefinition($this->prefix('csrf_token_factory'))
			->setType(CsrfTokenFactoryInterface::class)
			->setFactory($this->config->csrf_token_factory);

		if ($this->config->register_controls) {
			$builder->addFactoryDefinition($this->prefix('control.sign_in'))
				->setImplement(SignInControlFactoryInterface::class)
				->getResultDefinition()
				->setFactory(SignInControl::class);
		}

		$builder->addFactoryDefinition($this->prefix('query_object_factory.authenticator'))
			->setImplement(AuthenticatorQueryObjectFactoryInterface::class)
			->getResultDefinition()
			->setFactory(AuthenticatorQueryObject::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTargetEntities(): array
	{
		return [
			new TargetEntity(UserInterface::class, $this->sharedData[CommonExtension::SHARED_DATA_USER_CLASS_NAME]),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTranslationResources(): array
	{
		return $this->config->register_controls ? [ __DIR__ . '/../translations' ] : [];
	}
}
