<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Control\SignIn;

interface ISignInControlFactory
{
	/**
	 * @return \SixtyEightPublishers\User\Authentication\Control\SignIn\SignInControl
	 */
	public function create(): SignInControl;
}
