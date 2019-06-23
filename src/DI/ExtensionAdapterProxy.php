<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DI;

use Nette;
use SixtyEightPublishers;

final class ExtensionAdapterProxy implements IExtensionAdapter
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
	 */
	private function run(callable $cb) : void
	{
		if (TRUE === $this->propagationStopped) {
			return;
		}

		try {
			$cb();
		} catch (SixtyEightPublishers\User\Common\Exception\StopPropagationException $e) {
			$this->propagationStopped = TRUE;
		}
	}

	/**
	 * @return \SixtyEightPublishers\User\DI\IExtensionAdapter
	 */
	private function getExtensionAdapter() : IExtensionAdapter
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
	public static function getDefaults() : array
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
	public function loadConfiguration() : void
	{
		$this->run(function () {
			$this->getExtensionAdapter()->loadConfiguration();
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile() : void
	{
		$this->run(function () {
			$this->getExtensionAdapter()->beforeCompile();
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class) : void
	{
		$this->run(function () use ($class) {
			$this->getExtensionAdapter()->afterCompile($class);
		});
	}
}
