<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DI;

use Nette;
use SixtyEightPublishers;

abstract class AbstractExtensionAdapter implements IExtensionAdapter
{
	use Nette\SmartObject;

	/** @var array  */
	protected static $defaults = [];

	/** @var string  */
	protected $name;

	/** @var \Nette\DI\ContainerBuilder  */
	private $builder;

	/** @var array  */
	private $config;

	/**
	 * @param \Nette\DI\ContainerBuilder $builder
	 * @param string                     $name
	 * @param array                      $config
	 */
	public function __construct(Nette\DI\ContainerBuilder $builder, string $name, array $config)
	{
		$this->builder = $builder;
		$this->name = $name;
		$this->config = $this->processConfig($config);
	}

	/**
	 * @internal
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	protected function processConfig(array $config) : array
	{
		return $config;
	}

	/**
	 * @return void
	 * @throws \SixtyEightPublishers\User\Common\Exception\StopPropagationException
	 */
	protected function stopPropagation() : void
	{
		throw new SixtyEightPublishers\User\Common\Exception\StopPropagationException();
	}

	/**
	 * @return \Nette\DI\ContainerBuilder
	 */
	protected function getContainerBuilder() : Nette\DI\ContainerBuilder
	{
		return $this->builder;
	}

	/**
	 * @return array
	 */
	protected function getConfig() : array
	{
		return $this->config;
	}

	/**
	 * @param string $id
	 *
	 * @return string
	 */
	protected function prefix(string $id) : string
	{
		return substr_replace($id, $this->name . '.', substr($id, 0, 1) === '@' ? 1 : 0, 0);
	}

	/*************** interface \SixtyEightPublishers\User\DI\IExtensionAdapter ***************/

	/**
	 * {@inheritdoc}
	 */
	public static function getDefaults() : array
	{
		return static::$defaults;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration() : void
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile() : void
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class) : void
	{
	}
}
