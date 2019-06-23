<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DoctrineIdentity;

use Nette;

final class IdentityReference implements Nette\Security\IIdentity
{
	use Nette\SmartObject;

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
	public function getClassName() : string
	{
		return $this->className;
	}

	/************ interface \Nette\Security\IIdentity ************/

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
	public function getRoles() : array
	{
		return [];
	}
}
