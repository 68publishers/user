<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\DoctrineEntity;

interface IPasswordRequest
{
	const   DEFAULT_EXPIRATION  = '1 hour';

	const   STATUS_CREATED      = 'created',
			STATUS_COMPLETED    = 'completed',
			STATUS_CANCELED     = 'canceled';

	const STATUSES = [
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
	public function setStatus(string $status) : void;

	/**
	 * @return string
	 */
	public function getStatus() : string;

	/**
	 * @return \DateTime
	 */
	public function getCreated() : \DateTime;

	/**
	 * @return \DateTime
	 */
	public function getUpdated() : \DateTime;

	/**
	 * @return \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser
	 */
	public function getUser() : IUser;

	/**
	 * @return \DateTime
	 */
	public function getExpiration() : \DateTime;

	/**
	 * @return bool
	 */
	public function isExpired() : bool;

	/**
	 * @return \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\DeviceInfo
	 */
	public function getRequestDeviceInfo() : DeviceInfo;

	/**
	 * @return \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\DeviceInfo
	 */
	public function getResetDeviceInfo() : DeviceInfo;
}
