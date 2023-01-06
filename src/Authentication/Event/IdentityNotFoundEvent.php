<?php

namespace SixtyEightPublishers\User\Authentication\Event;

use Nette\Security\IIdentity;
use Symfony\Contracts\EventDispatcher\Event;

final class IdentityNotFoundEvent extends Event
{
	public const NAME = '68publishers.doctrine_identity.identity_not_found';

	private IIdentity $identityReference;

	/**
	 * @param \Nette\Security\IIdentity $identityReference
	 */
	public function __construct(IIdentity $identityReference)
	{
		$this->identityReference = $identityReference;
	}

	public function getIdentityReference(): IIdentity
	{
		return $this->identityReference;
	}
}
