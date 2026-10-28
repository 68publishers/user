<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Tests\Fixtures\Annotation;

use Doctrine\ORM\Mapping as ORM;
use SixtyEightPublishers\User\Common\DbalType\Password\PasswordInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface as ForgotPasswordUserInterface;

/**
 * An application entity mapped with annotations (doctrine/orm 2).
 *
 * @ORM\Entity
 * @ORM\Table(name="""user""")
 */
class User implements ForgotPasswordUserInterface
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=36)
	 */
	private string $id;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $email;

	public function __construct(string $id, string $email)
	{
		$this->id = $id;
		$this->email = $email;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getRoles(): array
	{
		return [];
	}

	public function getUsername(): string
	{
		return $this->email;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setPassword(?PasswordInterface $password): void
	{
	}
}
