<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DoctrineEntity\Accessor;

interface ILoginAccessor
{
	/**
	 * @return string
	 */
	public function getLogin(): string;
}
