<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Entity\Accessor;

interface PasswordAccessorInterface
{
	/**
	 * @return string
	 */
	public function getPassword(): string;
}
