<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DI;

use ArrayObject;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use SixtyEightPublishers\User\Common\DI\CommonExtension;
use SixtyEightPublishers\User\Authentication\DI\AuthenticationExtension;
use SixtyEightPublishers\User\Common\Exception\StopPropagationException;
use SixtyEightPublishers\User\ForgotPassword\DI\ForgotPasswordExtension;
use SixtyEightPublishers\User\DoctrineIdentity\DI\DoctrineIdentityExtension;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\DatabaseTypeProviderInterface;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\TargetEntityProviderInterface;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\EntityMappingProviderInterface;
use SixtyEightPublishers\TranslationBridge\Bridge\Nette\DI\TranslationProviderInterface;

final class UserBundleExtension extends CompilerExtension implements DatabaseTypeProviderInterface, EntityMappingProviderInterface, TargetEntityProviderInterface, TranslationProviderInterface
{
	/** @var \Nette\DI\CompilerExtension[] */
	private $extensions;

	public function __construct()
	{
		$this->extensions = [
			'common' => new CommonExtension(),
			'doctrine_identity' => new DoctrineIdentityExtension(),
			'forgot_password' => new ForgotPasswordExtension(),
			'authentication' => new AuthenticationExtension(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema
	{
		return Expect::structure(array_map(static function (CompilerExtension $extension) {
			return $extension->getConfigSchema();
		}, $this->extensions));
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$config = $this->config;
		$sharedData = new ArrayObject();

		foreach ($config as $extName => $extConfig) {
			$extension = $this->extensions[$extName];
			$extension->setCompiler($this->compiler, $this->prefix($extName));
			$extension->setConfig($extConfig);

			if ($extension instanceof CompilerExtensionPassInterface) {
				$extension->attach($this, $sharedData);
			}
		}

		foreach ($this->extensions as $name => $extension) {
			try {
				if ($extension instanceof CompilerExtensionPassInterface) {
					$extension->startup();
				}
			} catch (StopPropagationException $e) {
				unset($this->extensions[$name]);

				continue;
			}

			$extension->loadConfiguration();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeCompile(): void
	{
		foreach ($this->extensions as $extension) {
			$extension->beforeCompile();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function afterCompile(ClassType $class): void
	{
		foreach ($this->extensions as $extension) {
			$extension->afterCompile($class);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDatabaseTypes(): array
	{
		return array_merge([], ...array_map(static function (DatabaseTypeProviderInterface $provider) {
			return $provider->getDatabaseTypes();
		}, array_filter(array_values($this->extensions), static function ($extension) {
			return $extension instanceof DatabaseTypeProviderInterface;
		})));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityMappings(): array
	{
		return array_merge([], ...array_map(static function (EntityMappingProviderInterface $provider) {
			return $provider->getEntityMappings();
		}, array_filter(array_values($this->extensions), static function ($extension) {
			return $extension instanceof EntityMappingProviderInterface;
		})));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTargetEntities(): array
	{
		return array_merge([], ...array_map(static function (TargetEntityProviderInterface $provider) {
			return $provider->getTargetEntities();
		}, array_filter(array_values($this->extensions), static function ($extension) {
			return $extension instanceof TargetEntityProviderInterface;
		})));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTranslationResources(): array
	{
		return array_merge([], ...array_map(static function (TranslationProviderInterface $provider) {
			return $provider->getTranslationResources();
		}, array_filter(array_values($this->extensions), static function ($extension) {
			return $extension instanceof TranslationProviderInterface;
		})));
	}
}
