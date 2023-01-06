<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Authenticator;

use Nette\Security\IIdentity;
use Nette\Security\IdentityHandler;
use Nette\Security\Authenticator as AuthenticatorInterface;
use Doctrine\ORM\NonUniqueResultException;
use Nette\Security\AuthenticationException;
use Doctrine\DBAL\Exception as DBALException;
use SixtyEightPublishers\User\Authentication\Entity\UserInterface;
use SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface;
use SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryObjectFactoryInterface;

final class Authenticator implements AuthenticatorInterface, IdentityHandler
{
	public function __construct(
		private readonly ExecutableQueryObjectFactoryInterface $executableQueryObjectFactory,
		private readonly AuthenticatorQueryObjectFactoryInterface $authenticatorQueryFactory,
		private readonly IdentityHandler $identityHandler,
	) {}

	public function authenticate(string $username, string $password): IIdentity
	{
		$user = $this->findUser($username);

		if (null === $user->getPassword() || !$user->getPassword()->verify($password)) {
			throw new AuthenticationException(sprintf(
				'Invalid password for user "%s"',
				$username
			), self::INVALID_CREDENTIAL);
		}

		return $user;
	}

	public function sleepIdentity(IIdentity $identity): IIdentity
	{
		return $this->identityHandler->sleepIdentity($identity);
	}

	public function wakeupIdentity(IIdentity $identity): ?IIdentity
	{
		return $this->identityHandler->wakeupIdentity($identity);
	}

	/**
	 * @param string $username
	 *
	 * @return \SixtyEightPublishers\User\Authentication\Entity\UserInterface
	 * @throws \Nette\Security\AuthenticationException
	 */
	private function findUser(string $username): UserInterface
	{
		try {
			$user = $this->executableQueryObjectFactory->create($this->authenticatorQueryFactory->create($username))->fetchOne();
		} catch (NonUniqueResultException $e) {
			$e = new AuthenticationException(sprintf(
				'User\'s username field is not unique! Value was "%s"',
				$username
			), self::FAILURE, $e);
		} catch (DBALException $e) {
			$e = new AuthenticationException(sprintf(
				'DBAL throws unexpected exception, username value was "%s"',
				$username
			), self::FAILURE, $e);
		}

		if (isset($e)) {
			throw $e;
		}

		if (!isset($user)) {
			throw new AuthenticationException(sprintf(
				'User "%s" not found.',
				$username
			), self::IDENTITY_NOT_FOUND);
		}

		return $user;
	}
}
