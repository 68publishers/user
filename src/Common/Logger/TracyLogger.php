<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Logger;

use Nette;
use Tracy;

final class TracyLogger implements ILogger
{
	use Nette\SmartObject;

	/*************** interface \SixtyEightPublishers\User\Common\Logger\ILogger ***************/

	/**
	 * {@inheritdoc}
	 */
	public function error(string $message): void
	{
		Tracy\Debugger::log($message, Tracy\ILogger::ERROR);
	}

	/**
	 * {@inheritdoc}
	 */
	public function notice(string $message): void
	{
		Tracy\Debugger::log($message, Tracy\ILogger::WARNING);
	}

	/**
	 * {@inheritdoc}
	 */
	public function info(string $message): void
	{
		Tracy\Debugger::log($message, Tracy\ILogger::INFO);
	}
}
