<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Csrf;

interface ICsrfTokenFactory
{
	/**
	 * @param string $component
	 *
	 * @return string
	 */
	public function create(string $component = ''): string;
}
