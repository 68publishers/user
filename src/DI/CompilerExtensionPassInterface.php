<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DI;

use ArrayObject;
use Nette\DI\CompilerExtension;

interface CompilerExtensionPassInterface
{
	/**
	 * @param \Nette\DI\CompilerExtension $extension
	 * @param \ArrayObject                $sharedData
	 *
	 * @return void
	 */
	public function attach(CompilerExtension $extension, ArrayObject $sharedData): void;

	/**
	 * @return void
	 * @throws \SixtyEightPublishers\User\Common\Exception\StopPropagationException
	 */
	public function startup(): void;
}
