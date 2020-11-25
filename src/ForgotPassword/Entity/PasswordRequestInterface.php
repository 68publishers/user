<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Entity;

use DateTime;

interface PasswordRequestInterface
{
	public const DEFAULT_EXPIRATION  = '1 hour';

	public const STATUS_CREATED      = 'created';
	public const STATUS_COMPLETED    = 'completed';
	public const STATUS_CANCELED     = 'canceled';

	public const STATUSES = [
		self::STATUS_CREATED,
		self::STATUS_COMPLETED,
		self::STATUS_CANCELED,
	];

	/**
	 * @return mixed
	 */
	public function getId();

	/**
	 * @param string $status
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\User\Common\Exception\InvalidArgumentException
	 */
	public function setStatus(string $status): void;

	/**
	 * @return string
	 */
	public function getStatus(): string;

	/**
	 * @return \DateTime
	 */
	public function getCreated(): DateTime;

	/**
	 * @return \DateTime
	 */
	public function getUpdated(): DateTime;

	/**
	 * @return \SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface
	 */
	public function getUser(): UserInterface;

	/**
	 * @return \DateTime
	 */
	public function getExpiration(): DateTime;

	/**
	 * @return bool
	 */
	public function isExpired(): bool;

	/**
	 * @return \SixtyEightPublishers\User\ForgotPassword\Entity\DeviceInfo
	 */
	public function getRequestDeviceInfo(): DeviceInfo;

	/**
	 * @return \SixtyEightPublishers\User\ForgotPassword\Entity\DeviceInfo
	 */
	public function getResetDeviceInfo(): DeviceInfo;
}
