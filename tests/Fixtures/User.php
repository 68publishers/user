<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Tests\Fixtures;

use SixtyEightPublishers\User\Common\DbalType\Password\PasswordInterface;
use SixtyEightPublishers\User\Authentication\Entity\UserInterface as AuthenticationUserInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface as ForgotPasswordUserInterface;

final class User implements AuthenticationUserInterface, ForgotPasswordUserInterface
{
	public ?PasswordInterface $password = NULL;

	public function getId(): string
	{
		return '0b6ef2a3-0d2b-4b8e-9d7c-1b2f6b8e2a51';
	}

	public function getRoles(): array
	{
		return [];
	}

	public function getUsername(): string
	{
		return 'john';
	}

	public function getEmail(): string
	{
		return 'john@example.com';
	}

	public function getPassword(): ?PasswordInterface
	{
		return $this->password;
	}

	public function setPassword(?PasswordInterface $password): void
	{
		$this->password = $password;
	}
}
