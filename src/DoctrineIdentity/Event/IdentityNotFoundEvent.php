<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DoctrineIdentity\Event;

use Symfony\Contracts\EventDispatcher\Event;
use SixtyEightPublishers\User\DoctrineIdentity\IdentityReference;

final class IdentityNotFoundEvent extends Event
{
	public const NAME = '68publishers.doctrine_identity.identity_not_found';

	/** @var \SixtyEightPublishers\User\DoctrineIdentity\IdentityReference  */
	private $identityReference;

	/** @var string|NULL  */
	private $namespace;

	/**
	 * @param \SixtyEightPublishers\User\DoctrineIdentity\IdentityReference $identityReference
	 * @param string|NULL                                                   $namespace
	 */
	public function __construct(IdentityReference $identityReference, ?string $namespace)
	{
		$this->identityReference = $identityReference;
		$this->namespace = $namespace;
	}

	/**
	 * @return \SixtyEightPublishers\User\DoctrineIdentity\IdentityReference
	 */
	public function getIdentityReference(): IdentityReference
	{
		return $this->identityReference;
	}

	/**
	 * @return NULL|string
	 */
	public function getNamespace(): ?string
	{
		return $this->namespace;
	}
}
