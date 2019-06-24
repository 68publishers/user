<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use SixtyEightPublishers;

interface IPasswordRequestManager
{
	/**
	 * @param mixed $uid User's ID
	 * @param mixed $rid PasswordRequest's ID
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException
	 */
	public function findRequest($uid, $rid): SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest
	 * @param string                                                                    $password
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException
	 */
	public function reset(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest, string $password): void;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException
	 */
	public function cancel(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest): void;
}
