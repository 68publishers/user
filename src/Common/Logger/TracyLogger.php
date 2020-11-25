<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Logger;

use Tracy\ILogger;
use Tracy\Debugger;
use Nette\SmartObject;

final class TracyLogger implements LoggerInterface
{
	use SmartObject;

	/**
	 * {@inheritdoc}
	 */
	public function error(string $message): void
	{
		Debugger::log($message, ILogger::ERROR);
	}

	/**
	 * {@inheritdoc}
	 */
	public function notice(string $message): void
	{
		Debugger::log($message, ILogger::WARNING);
	}

	/**
	 * {@inheritdoc}
	 */
	public function info(string $message): void
	{
		Debugger::log($message, ILogger::INFO);
	}
}
