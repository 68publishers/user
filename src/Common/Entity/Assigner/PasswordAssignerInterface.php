<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Entity\Assigner;

interface PasswordAssignerInterface
{
	/**
	 * @param string $password
	 *
	 * @return void
	 */
	public function setPassword(string $password): void;
}
