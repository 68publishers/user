<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword;

use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;

interface ResetPasswordControlFactoryInterface
{
	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface $passwordRequest
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControl
	 */
	public function create(PasswordRequestInterface $passwordRequest): ResetPasswordControl;
}
