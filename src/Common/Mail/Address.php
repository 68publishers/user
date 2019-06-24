<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Mail;

use Nette;

/**
 * @property-read string $email
 * @property-read NULL|string $name
 */
final class Address
{
	use Nette\SmartObject;

	/** @var string  */
	private $email;

	/** @var string|NULL  */
	private $name;

	/**
	 * @param string      $email
	 * @param string|NULL $name
	 */
	public function __construct(string $email, ?string $name = NULL)
	{
		$this->email = $email;
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @return NULL|string
	 */
	public function getName(): ?string
	{
		return $this->name;
	}
}
