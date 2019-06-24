<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DI;

use Nette;
use SixtyEightPublishers;

final class ExtensionAdapterFactory
{
	use Nette\SmartObject;

	/** @var \Nette\DI\ContainerBuilder  */
	private $builder;

	/** @var \ArrayObject  */
	private $sharedData;

	/**
	 * @param \Nette\DI\ContainerBuilder $builder
	 * @param \ArrayObject               $sharedData
	 */
	public function __construct(Nette\DI\ContainerBuilder $builder, \ArrayObject $sharedData)
	{
		$this->builder = $builder;
		$this->sharedData = $sharedData;
	}

	/**
	 * @param string $className
	 * @param string $name
	 * @param array  $config
	 *
	 * @return \SixtyEightPublishers\User\DI\IExtensionAdapter
	 * @throws \SixtyEightPublishers\User\Common\Exception\InvalidArgumentException
	 */
	public function create(string $className, string $name, array $config): IExtensionAdapter
	{
		if (!is_subclass_of($className, IExtensionAdapter::class, TRUE)) {
			throw new SixtyEightPublishers\User\Common\Exception\InvalidArgumentException(sprintf(
				'Passed classname must be implementor of %s interface',
				IExtensionAdapter::class
			));
		}

		return new ExtensionAdapterProxy(function () use ($className, $name, $config) {
			return new $className($this->builder, $name, $config, $this->sharedData);
		});
	}
}
