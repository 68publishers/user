<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Entity\Accessor;

interface EmailAccessorInterface
{
	/**
	 * @return string
	 */
	public function getEmail(): string;
}
