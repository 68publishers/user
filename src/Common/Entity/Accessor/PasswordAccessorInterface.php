<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Entity\Accessor;

use SixtyEightPublishers\User\Common\DbalType\Password\PasswordInterface;

interface PasswordAccessorInterface
{
	/**
	 * @return \SixtyEightPublishers\User\Common\DbalType\Password\PasswordInterface|null
	 */
	public function getPassword(): ?PasswordInterface;
}
