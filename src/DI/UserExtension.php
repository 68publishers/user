<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DI;

use Kdyby;
use Nette;
use SixtyEightPublishers;

final class UserExtension extends Nette\DI\CompilerExtension implements
	Kdyby\Doctrine\DI\IEntityProvider,
	Kdyby\Doctrine\DI\ITargetEntityProvider,
	Kdyby\Translation\DI\ITranslationProvider
{
	/** @var array  */
	private $defaults = [
		# 'common' is loaded in constructor
		# 'doctrine_identity' is loaded in constructor
		# 'forgot_password' is loaded in constructor
	];

	/** @var string[]|\SixtyEightPublishers\User\DI\IExtensionAdapter[]  */
	private $extensionAdapters = [
		'common' => SixtyEightPublishers\User\Common\DI\CommonExtensionAdapter::class,
		'doctrine_identity' => SixtyEightPublishers\User\DoctrineIdentity\DI\DoctrineIdentityExtensionAdapter::class,
		'forgot_password' => SixtyEightPublishers\User\ForgotPassword\DI\ForgotPasswordExtensionAdapter::class,
	];

	/** @var bool  */
	private $extensionAdaptersBuilded = FALSE;

	public function __construct()
	{
		$this->defaults = array_merge($this->defaults, [
			'common' => SixtyEightPublishers\User\Common\DI\CommonExtensionAdapter::getDefaults(),
			'doctrine_identity' => SixtyEightPublishers\User\DoctrineIdentity\DI\DoctrineIdentityExtensionAdapter::getDefaults(),
			'forgot_password' => SixtyEightPublishers\User\ForgotPassword\DI\ForgotPasswordExtensionAdapter::getDefaults(),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		foreach ($this->getExtensionAdapters() as $extensionAdapter) {
			$extensionAdapter->loadConfiguration();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		foreach ($this->getExtensionAdapters() as $extensionAdapter) {
			$extensionAdapter->beforeCompile();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class): void
	{
		foreach ($this->getExtensionAdapters() as $extensionAdapter) {
			$extensionAdapter->afterCompile($class);
		}
	}

	/**
	 * @param array $config
	 */
	private function buildExtensionAdapters(array $config): void
	{
		$extensionAdapterFactory = new ExtensionAdapterFactory($this->getContainerBuilder(), new \ArrayObject());

		foreach ($this->extensionAdapters as $name => $className) {
			$this->extensionAdapters[$name] = $extensionAdapterFactory->create(
				$className,
				$this->prefix($name),
				$config[$name]
			);
		}
	}

	/**
	 * @return \SixtyEightPublishers\User\DI\IExtensionAdapter[]
	 */
	private function getExtensionAdapters(): array
	{
		if (FALSE === $this->extensionAdaptersBuilded) {
			/** @noinspection PhpInternalEntityUsedInspection */
			$config = $this->validateConfig(Nette\DI\Helpers::expand($this->defaults, $this->getContainerBuilder()->parameters));

			$this->buildExtensionAdapters($config);
			$this->extensionAdaptersBuilded = TRUE;
		}

		return $this->extensionAdapters;
	}

	/**************** interface \Kdyby\Doctrine\DI\IEntityProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getEntityMappings(): array
	{
		$map =  array_values(array_map(
			function (Kdyby\Doctrine\DI\IEntityProvider $provider) {
				return $provider->getEntityMappings();
			},
			$this->getExtensionAdapters()
		));

		return 0 < count($map) ? array_merge(...$map) : [];
	}

	/**************** interface \Kdyby\Doctrine\DI\ITargetEntityProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getTargetEntityMappings(): array
	{
		$map =  array_values(array_map(
			function (Kdyby\Doctrine\DI\ITargetEntityProvider $provider) {
				return $provider->getTargetEntityMappings();
			},
			$this->getExtensionAdapters()
		));

		return 0 < count($map) ? array_merge(...$map) : [];
	}

	/**************** interface \Kdyby\Translation\DI\ITranslationProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getTranslationResources(): array
	{
		$map =  array_values(array_map(
			function (Kdyby\Translation\DI\ITranslationProvider $provider) {
				return $provider->getTranslationResources();
			},
			$this->getExtensionAdapters()
		));

		return 0 < count($map) ? array_merge(...$map) : [];
	}
}
