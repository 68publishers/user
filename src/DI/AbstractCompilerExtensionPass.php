<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DI;

use ArrayObject;
use Nette\DI\CompilerExtension;
use SixtyEightPublishers\User\Common\Exception\StopPropagationException;

abstract class AbstractCompilerExtensionPass extends CompilerExtension implements CompilerExtensionPassInterface
{
	/** @var \Nette\DI\CompilerExtension|NULL */
	protected $parent;

	/** @var ArrayObject|NULL */
	protected $sharedData;

	/**
	 * {@inheritDoc}
	 */
	public function attach(CompilerExtension $extension, ArrayObject $sharedData): void
	{
		$this->parent = $extension;
		$this->sharedData = $sharedData;
		$this->initialization = $extension->getInitialization();
	}

	/**
	 * {@inheritDoc}
	 */
	public function startup(): void
	{
	}

	/**
	 * @return void
	 */
	protected function stopPropagation(): void
	{
		throw new StopPropagationException();
	}
}
