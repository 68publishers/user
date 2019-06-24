<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DoctrineEntity\Accessor;

interface IEmailAccessor
{
	/**
	 * @return string
	 */
	public function getEmail(): string;
}
