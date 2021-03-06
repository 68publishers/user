<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Control\ForgotPassword;

interface ForgotPasswordControlFactoryInterface
{
	/**
	 * @return \SixtyEightPublishers\User\ForgotPassword\Control\ForgotPassword\ForgotPasswordControl
	 */
	public function create(): ForgotPasswordControl;
}
