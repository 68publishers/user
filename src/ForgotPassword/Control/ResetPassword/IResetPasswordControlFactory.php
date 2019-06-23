<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword;

use SixtyEightPublishers;

interface IResetPasswordControlFactory
{
	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControl
	 */
	public function create(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest) : ResetPasswordControl;
}
