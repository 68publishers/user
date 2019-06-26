<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DoctrineEntity\Accessor;

interface IUsernameAccessor
{
	/**
	 * @return string
	 */
	public function getUsername(): string;
}
