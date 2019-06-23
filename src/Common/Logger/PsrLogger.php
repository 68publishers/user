<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Logger;

use Psr;
use Nette;

final class PsrLogger implements ILogger
{
	use Nette\SmartObject;

	/** @var \Psr\Log\LoggerInterface  */
	private $logger;

	/**
	 * @param \Psr\Log\LoggerInterface $logger
	 */
	public function __construct(Psr\Log\LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/*************** interface \SixtyEightPublishers\User\Common\Logger\ILogger ***************/

	/**
	 * {@inheritdoc}
	 */
	public function error(string $message) : void
	{
		$this->logger->error($message);
	}

	/**
	 * {@inheritdoc}
	 */
	public function notice(string $message) : void
	{
		$this->logger->notice($message);
	}

	/**
	 * {@inheritdoc}
	 */
	public function info(string $message) : void
	{
		$this->logger->info($message);
	}
}
