<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DoctrineIdentity;

use Nette\SmartObject;
use Nette\Security\IIdentity;

final class IdentityReference implements IIdentity
{
	use SmartObject;

	/** @var string  */
	private $className;

	/** @var  */
	private $id;

	/**
	 * @param string $className
	 * @param mixed  $id
	 */
	public function __construct(string $className, $id)
	{
		$this->className = $className;
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getClassName(): string
	{
		return $this->className;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRoles(): array
	{
		return [];
	}
}
