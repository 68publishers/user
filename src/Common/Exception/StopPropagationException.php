<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Exception;

class StopPropagationException extends RuntimeException
{
	public function __construct()
	{
		parent::__construct('');
	}
}
