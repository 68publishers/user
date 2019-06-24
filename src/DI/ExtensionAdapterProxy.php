<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DI;

use Kdyby;
use Nette;
use SixtyEightPublishers;

final class ExtensionAdapterProxy implements
	IExtensionAdapter,
	Kdyby\Doctrine\DI\IEntityProvider,
	Kdyby\Doctrine\DI\ITargetEntityProvider,
	Kdyby\Translation\DI\ITranslationProvider
{
	use Nette\SmartObject;

	/** @var callable  */
	private $extensionAdapterFactory;

	/** @var \SixtyEightPublishers\User\DI\IExtensionAdapter  */
	private $extensionAdapter;

	/** @var bool  */
	private $propagationStopped = FALSE;

	/**
	 * @param callable $extensionAdapterFactory
	 */
	public function __construct(callable $extensionAdapterFactory)
	{
		$this->extensionAdapterFactory = $extensionAdapterFactory;
	}

	/**
	 * @param callable $cb
	 *
	 * @return mixed
	 */
	private function run(callable $cb)
	{
		if (TRUE === $this->propagationStopped) {
			return NULL;
		}

		try {
			return $cb();
		} catch (SixtyEightPublishers\User\Common\Exception\StopPropagationException $e) {
			$this->propagationStopped = TRUE;
		}

		return NULL;
	}

	/**
	 * @return \SixtyEightPublishers\User\DI\IExtensionAdapter
	 */
	private function getExtensionAdapter(): IExtensionAdapter
	{
		if (NULL === $this->extensionAdapter) {
			$factory = $this->extensionAdapterFactory;
			$this->extensionAdapter = $factory();
		}

		return $this->extensionAdapter;
	}

	/*************** interface \SixtyEightPublishers\User\DI\IExtensionAdapter ***************/

	/**
	 * {@inheritdoc}
	 */
	public static function getDefaults(): array
	{
		throw new SixtyEightPublishers\User\Common\Exception\RuntimeException(sprintf(
			'Can not call static method %s, object %s is just proxy.',
			__METHOD__,
			__CLASS__
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		$this->run(function () {
			$this->getExtensionAdapter()->loadConfiguration();
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		$this->run(function () {
			$this->getExtensionAdapter()->beforeCompile();
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class): void
	{
		$this->run(function () use ($class) {
			$this->getExtensionAdapter()->afterCompile($class);
		});
	}

	/**************** interface \Kdyby\Doctrine\DI\IEntityProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getEntityMappings(): array
	{
		return $this->run(function () {
			$adapter = $this->getExtensionAdapter();

			return $adapter instanceof Kdyby\Doctrine\DI\IEntityProvider ? $adapter->getEntityMappings() : [];
		}) ?? [];
	}

	/**************** interface \Kdyby\Doctrine\DI\ITargetEntityProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getTargetEntityMappings(): array
	{
		return $this->run(function () {
			$adapter = $this->getExtensionAdapter();

			return $adapter instanceof Kdyby\Doctrine\DI\ITargetEntityProvider ? $adapter->getTargetEntityMappings() : [];
		}) ?? [];
	}

	/**************** interface \Kdyby\Translation\DI\ITranslationProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getTranslationResources(): array
	{
		return $this->run(function () {
			$adapter = $this->getExtensionAdapter();

			return $adapter instanceof Kdyby\Translation\DI\ITranslationProvider ? $adapter->getTranslationResources() : [];
		}) ?? [];
	}
}
