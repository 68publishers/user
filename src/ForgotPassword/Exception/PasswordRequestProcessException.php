<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Exception;

use SixtyEightPublishers;

final class PasswordRequestProcessException extends SixtyEightPublishers\User\Common\Exception\RuntimeException
{
	const   CODE_MISSING_REQUEST = 2001,
			CODE_EXPIRED_REQUEST = 2002;

	/** @var mixed  */
	private $uid;

	/** @var mixed  */
	private $rid;

	/**
	 * @param mixed           $uid
	 * @param mixed           $rid
	 * @param string          $message
	 * @param int             $code
	 * @param \Throwable|null $previous
	 */
	public function __construct($uid, $rid, string $message, int $code = 0, \Throwable $previous = NULL)
	{
		parent::__construct($message, $code, $previous);

		$this->uid = $uid;
		$this->rid = $rid;
	}

	/**
	 * @param mixed $uid
	 * @param mixed $rid
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException
	 */
	public static function missingRequest($uid, $rid) : self
	{
		return new static($uid, $rid, sprintf(
			'Missing request for UID %s and RID %s',
			(string) $uid,
			(string) $rid
		), self::CODE_MISSING_REQUEST);
	}

	/**
	 * @param mixed $uid
	 * @param mixed $rid
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException
	 */
	public static function expiredRequest($uid, $rid) : self
	{
		return new static($uid, $rid, sprintf(
			'Request for UID %s and RID %s is already expired',
			(string) $uid,
			(string) $rid
		), self::CODE_EXPIRED_REQUEST);
	}

	/**
	 * @return mixed
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * @return mixed
	 */
	public function getRid()
	{
		return $this->rid;
	}

	/**
	 * @return bool
	 */
	public function isMissingRequest() : bool
	{
		return $this->code === self::CODE_MISSING_REQUEST;
	}

	/**
	 * @return bool
	 */
	public function isExpiredRequest() : bool
	{
		return $this->code === self::CODE_EXPIRED_REQUEST;
	}
}
