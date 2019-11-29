<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DoctrineIdentity;

use Nette;
use Doctrine;

/**
 * @method void onEntityNotFound()
 */
final class UserStorageProxy implements Nette\Security\IUserStorage
{
	use Nette\SmartObject;

	/** @var \Nette\Security\IUserStorage  */
	private $userStorage;

	/** @var \Doctrine\ORM\EntityManagerInterface  */
	private $em;

	/** @var \Nette\Security\Identity|NULL|bool */
	private $currentIdentity = FALSE;


	/** @var callable[] */
	public $onEntityNotFound = [];

	/**
	 * @param \Nette\Security\IUserStorage         $userStorage
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(Nette\Security\IUserStorage $userStorage, Doctrine\ORM\EntityManagerInterface $em)
	{
		$this->userStorage = $userStorage;
		$this->em = $em;
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
		if (!is_callable([ $this->userStorage, 'setNamespace' ])) {
			throw Exception\UnimplementedMethodException::unimplementedMethod(get_class($this->userStorage), 'setNamespace');
		}

		/** @noinspection PhpUndefinedMethodInspection */
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
		if (!is_callable([ $this->userStorage, 'getNamespace' ])) {
			throw Exception\UnimplementedMethodException::unimplementedMethod(get_class($this->userStorage), 'getNamespace');
		}

		/** @noinspection PhpUndefinedMethodInspection */
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
	public function setIdentity(?Nette\Security\IIdentity $identity = NULL): self
	{
		if (NULL !== $identity) {
			try {
				$metadata = $this->em->getMetadataFactory()->getMetadataFor(get_class($identity));

				$identity = new IdentityReference(
					$metadata->getName(),
					$metadata->getIdentifierValues($identity)
				);
			} catch (Doctrine\Common\Persistence\Mapping\MappingException $e) {
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
	public function getIdentity(): ?Nette\Security\IIdentity
	{
		if (FALSE !== $this->currentIdentity) {
			return $this->currentIdentity;
		}

		$identity = $this->userStorage->getIdentity();

		if (!$identity instanceof IdentityReference) {
			return $identity;
		}

		$identity = $this->em->find($identity->getClassName(), $identity->getId());

		if (!$identity instanceof Nette\Security\IIdentity) {
			$identity = NULL;

			$this->setAuthenticated(FALSE);
			$this->setIdentity($identity);

			$this->onEntityNotFound();
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
