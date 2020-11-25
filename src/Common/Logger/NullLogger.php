<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Logger;

use Nette\SmartObject;

final class NullLogger implements LoggerInterface
{
	use SmartObject;

	/**
	 * {@inheritdoc}
	 */
	public function error(string $message): void
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function notice(string $message): void
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function info(string $message): void
	{
	}
}
