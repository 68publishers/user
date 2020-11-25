<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Logger;

use Nette\SmartObject;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

final class PsrLogger implements LoggerInterface
{
	use SmartObject;

	/** @var \Psr\Log\LoggerInterface  */
	private $logger;

	/**
	 * @param \Psr\Log\LoggerInterface $logger
	 */
	public function __construct(PsrLoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * {@inheritdoc}
	 */
	public function error(string $message): void
	{
		$this->logger->error($message);
	}

	/**
	 * {@inheritdoc}
	 */
	public function notice(string $message): void
	{
		$this->logger->notice($message);
	}

	/**
	 * {@inheritdoc}
	 */
	public function info(string $message): void
	{
		$this->logger->info($message);
	}
}
