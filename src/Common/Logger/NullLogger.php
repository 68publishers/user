<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Logger;

use Nette;

final class NullLogger implements ILogger
{
	use Nette\SmartObject;

	/*************** interface \SixtyEightPublishers\User\Common\Logger\ILogger ***************/

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
