<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DoctrineIdentity;

use Nette\SmartObject;
use Nette\Security\IIdentity;
use Nette\Security\IUserStorage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use SixtyEightPublishers\User\DoctrineIdentity\Event\IdentityNotFoundEvent;
use SixtyEightPublishers\User\DoctrineIdentity\Exception\UnimplementedMethodException;

/**
 * @method void onIdentityNotFound(IdentityReference $identityReference)
 */
final class UserStorageProxy implements IUserStorage
{
	use SmartObject;

	/** @var \Nette\Security\IUserStorage  */
	private $userStorage;

	/** @var \Doctrine\ORM\EntityManagerInterface  */
	private $em;

	/** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface  */
	private $eventDispatcher;

	/** @var \Nette\Security\Identity|NULL|bool */
	private $currentIdentity = FALSE;

	/** @var callable[] */
	public $onIdentityNotFound = [];

	/**
	 * @param \Nette\Security\IUserStorage                                $userStorage
	 * @param \Doctrine\ORM\EntityManagerInterface                        $em
	 * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher
	 */
	public function __construct(IUserStorage $userStorage, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
	{
		$this->userStorage = $userStorage;
		$this->em = $em;
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * For compatibility with default implementation Nette\Http\UserStorage
	 *
	 * @param string $namespace
	 *
	 * @return \SixtyEightPublishers\User\DoctrineIdentity\UserStorageProxy
	 * @throws \SixtyEightPublishers\User\DoctrineIdentity\Exception\UnimplementedMethodException
	 */
	public function setNamespace(string $namespace): self
	{
		if (!is_callable([$this->userStorage, 'setNamespace'])) {
			throw UnimplementedMethodException::unimplementedMethod(get_class($this->userStorage), 'setNamespace');
		}

		$this->userStorage->setNamespace($namespace);

		return $this;
	}

	/**
	 * For compatibility with default implementation Nette\Http\UserStorage
	 *
	 * @return string
	 * @throws \SixtyEightPublishers\User\DoctrineIdentity\Exception\UnimplementedMethodException
	 */
	public function getNamespace(): string
	{
		if (!is_callable([$this->userStorage, 'getNamespace'])) {
			throw Exception\UnimplementedMethodException::unimplementedMethod(get_class($this->userStorage), 'getNamespace');
		}

		return $this->userStorage->getNamespace();
	}

	/************ interface \Nette\Security\IUserStorage ************/

	/**
	 * {@inheritdoc}
	 */
	public function setAuthenticated($state): self
	{
		$this->userStorage->setAuthenticated($state);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAuthenticated(): bool
	{
		# Get the identity as first. If the entity is not found then the user can't be authenticated.
		$this->getIdentity();

		return $this->userStorage->isAuthenticated();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setIdentity(?IIdentity $identity = NULL): self
	{
		if (NULL !== $identity) {
			try {
				$metadata = $this->em->getMetadataFactory()->getMetadataFor(get_class($identity));

				$identity = new IdentityReference(
					$metadata->getName(),
					$metadata->getIdentifierValues($identity)
				);
			} catch (MappingException $e) {
				# an empty catch block because we can't test if the MetadataFactory contains a metadata for identity's classname.
				# The classname can be a Doctrine Proxy and the method `MetadataFactory::hasMetadataFor()` doesn't convert Proxy's classname into real classname.
			}
		}

		$this->userStorage->setIdentity($identity);
		$this->currentIdentity = FALSE;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIdentity(): ?IIdentity
	{
		if (FALSE !== $this->currentIdentity) {
			return $this->currentIdentity;
		}

		$identityReference = $this->userStorage->getIdentity();

		if (!$identityReference instanceof IdentityReference) {
			return $identityReference;
		}

		$identity = $this->em->find($identityReference->getClassName(), $identityReference->getId());

		if (!$identity instanceof IIdentity) {
			$identity = NULL;

			$this->setAuthenticated(FALSE);
			$this->setIdentity($identity);

			$namespace = $this->getNamespace();

			$this->eventDispatcher->dispatch(new IdentityNotFoundEvent($identityReference, empty($namespace) ? NULL : $namespace), IdentityNotFoundEvent::NAME);
			$this->onIdentityNotFound($identityReference);
		}

		return $this->currentIdentity = $identity;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setExpiration($time, $flags = 0): self
	{
		$this->userStorage->setExpiration($time, $flags);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLogoutReason(): ?int
	{
		return $this->userStorage->getLogoutReason();
	}
}
