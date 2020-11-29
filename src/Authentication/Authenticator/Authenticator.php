<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Authenticator;

use Nette\SmartObject;
use Nette\Security\IIdentity;
use Nette\Security\IAuthenticator;
use Doctrine\ORM\NonUniqueResultException;
use Nette\Security\AuthenticationException;
use Doctrine\DBAL\Exception as DBALException;
use SixtyEightPublishers\User\Authentication\Entity\UserInterface;
use SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface;
use SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryObjectFactoryInterface;

final class Authenticator implements IAuthenticator
{
	use SmartObject;

	/** @var \SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface  */
	private $executableQueryObjectFactory;

	/** @var \SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryObjectFactoryInterface  */
	private $authenticatorQueryFactory;

	/**
	 * @param \SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface         $executableQueryObjectFactory
	 * @param \SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryObjectFactoryInterface $authenticatorQueryFactory
	 */
	public function __construct(ExecutableQueryObjectFactoryInterface $executableQueryObjectFactory, AuthenticatorQueryObjectFactoryInterface $authenticatorQueryFactory)
	{
		$this->executableQueryObjectFactory = $executableQueryObjectFactory;
		$this->authenticatorQueryFactory = $authenticatorQueryFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function authenticate(array $credentials): IIdentity
	{
		[ $username, $password ] = $this->validateCredentials($credentials);
		$user = $this->findUser($username);

		if (!$user->getPassword()->verify($password)) {
			throw new AuthenticationException(sprintf(
				'Invalid password for user "%s"',
				$username
			), self::INVALID_CREDENTIAL);
		}

		return $user;
	}

	/**
	 * @param array $credentials
	 *
	 * @return array
	 * @throws \Nette\Security\AuthenticationException
	 */
	private function validateCredentials(array $credentials): array
	{
		if (!isset($credentials[self::USERNAME])) {
			throw new AuthenticationException(sprintf(
				'Missing username field in credentials (key %s)',
				self::USERNAME
			), self::FAILURE);
		}

		if (!isset($credentials[self::PASSWORD])) {
			throw new AuthenticationException(sprintf(
				'Missing password field in credentials (key %s)',
				self::PASSWORD
			), self::FAILURE);
		}

		return [
			(string) $credentials[self::USERNAME],
			(string) $credentials[self::PASSWORD],
		];
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
