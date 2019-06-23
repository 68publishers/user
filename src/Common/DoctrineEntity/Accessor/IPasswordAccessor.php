<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DoctrineEntity\Accessor;

interface IPasswordAccessor
{
	/**
	 * @return string
	 */
	public function getPassword() : string;
}
