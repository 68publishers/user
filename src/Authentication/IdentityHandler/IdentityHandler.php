<?php

namespace SixtyEightPublishers\User\Authentication\IdentityHandler;

use Nette\Security\IIdentity;
use Nette\Security\SimpleIdentity;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\User\Common\Entity\UserInterface;
use Nette\Security\IdentityHandler as IdentityHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use SixtyEightPublishers\User\Authentication\Event\IdentityNotFoundEvent;

final class IdentityHandler implements IdentityHandlerInterface
{
	public function __construct(
		private readonly EntityManagerInterface $em,
		private readonly ?EventDispatcherInterface $eventDispatcher = null
	) {}

	public function sleepIdentity(IIdentity $identity): IIdentity
	{
		if ($identity instanceof UserInterface) {
			$identity = new SimpleIdentity((string) $identity->getId(), $identity->getRoles(), []);
		}

		return $identity;
	}

	public function wakeupIdentity(IIdentity $identity): ?IIdentity
	{
		if ($identity instanceof UserInterface) {
			return $identity;
		}

		$user = $this->em->find(UserInterface::class, $identity->getId());

		if ($user instanceof UserInterface) {
			return $user;
		}

		$this->eventDispatcher?->dispatch(new IdentityNotFoundEvent($identity));

		return null;
	}
}
