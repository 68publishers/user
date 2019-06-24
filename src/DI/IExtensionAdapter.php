<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DI;

use Nette;

interface IExtensionAdapter
{
	/**
	 * @return array
	 */
	public static function getDefaults(): array;

	/**
	 * @return void
	 */
	public function loadConfiguration(): void;


	/**
	 * @return void
	 */
	public function beforeCompile(): void;


	/**
	 * @param \Nette\PhpGenerator\ClassType $class
	 *
	 * @return void
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class): void;
}
