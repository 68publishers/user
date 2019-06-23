<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DoctrineEntity\Assigner;

interface IPasswordAssigner
{
	/**
	 * @param string $password
	 *
	 * @return void
	 */
	public function setPassword(string $password) : void;
}
