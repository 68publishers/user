<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Control\SignIn;

interface SignInControlFactoryInterface
{
	/**
	 * @return \SixtyEightPublishers\User\Authentication\Control\SignIn\SignInControl
	 */
	public function create(): SignInControl;
}
