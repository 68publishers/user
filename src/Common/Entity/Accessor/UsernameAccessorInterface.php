<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Entity\Accessor;

interface UsernameAccessorInterface
{
	/**
	 * @return string
	 */
	public function getUsername(): string;
}
