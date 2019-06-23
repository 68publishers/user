<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DoctrineIdentity;

use Nette;
use Doctrine;

final class UserStorageProxy implements Nette\Security\IUserStorage
{
	use Nette\SmartObject;

	/** @var \Nette\Security\IUserStorage  */
	private $userStorage;

	/** @var \Doctrine\ORM\EntityManagerInterface  */
	private $em;

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
	public function setNamespace(string $namespace) : self
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
	public function getNamespace() : string
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
	public function setAuthenticated($state) : self
	{
		$this->userStorage->setAuthenticated($state);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAuthenticated() : bool
	{
		return $this->userStorage->isAuthenticated();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setIdentity(?Nette\Security\IIdentity $identity = NULL) : self
	{
		if (NULL !== $identity) {
			/** @noinspection PhpDeprecationInspection */
			$className = Doctrine\Common\Util\ClassUtils::getClass($identity);
			$metadataFactory = $this->em->getMetadataFactory();

			if ($metadataFactory->hasMetadataFor($className)) {
				$identity = new IdentityReference(
					$className,
					$metadataFactory->getMetadataFor($className)->getIdentifierValues($identity)
				);
			}
		}

		$this->userStorage->setIdentity($identity);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIdentity() : ?Nette\Security\IIdentity
	{
		$identity = $this->userStorage->getIdentity();

		if ($identity instanceof IdentityReference) {
			$identity = $this->em->getReference($identity->getClassName(), $identity->getId());
		}

		return $identity;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setExpiration($time, $flags = 0) : self
	{
		$this->userStorage->setExpiration($time, $flags);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLogoutReason() : ?int
	{
		return $this->userStorage->getLogoutReason();
	}
}
