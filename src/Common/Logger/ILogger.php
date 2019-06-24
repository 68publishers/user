<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Logger;

interface ILogger
{
	/**
	 * @param string $message
	 *
	 * @return void
	 */
	public function error(string $message): void;

	/**
	 * @param string $message
	 *
	 * @return void
	 */
	public function notice(string $message): void;

	/**
	 * @param string $message
	 *
	 * @return void
	 */
	public function info(string $message): void;
}
