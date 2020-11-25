<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;

interface PasswordRequestManagerInterface
{
	/**
	 * @param mixed $uid User's ID
	 * @param mixed $rid PasswordRequest's ID
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException
	 */
	public function findRequest($uid, $rid): PasswordRequestInterface;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface $passwordRequest
	 * @param string                                                                    $password
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException
	 */
	public function reset(PasswordRequestInterface $passwordRequest, string $password): void;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface $passwordRequest
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException
	 */
	public function cancel(PasswordRequestInterface $passwordRequest): void;
}
