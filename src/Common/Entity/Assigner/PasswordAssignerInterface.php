<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Entity\Assigner;

use SixtyEightPublishers\User\Common\DbalType\Password\PasswordInterface;

interface PasswordAssignerInterface
{
	/**
	 * @param \SixtyEightPublishers\User\Common\DbalType\Password\PasswordInterface $password
	 *
	 * @return void
	 */
	public function setPassword(PasswordInterface $password): void;
}
