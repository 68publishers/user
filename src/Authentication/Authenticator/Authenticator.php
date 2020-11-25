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
use SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface;
use SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryObjectFactoryInterface;

final class Authenticator implements IAuthenticator
{
	use SmartObject;

	/** @var \SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface  */
	private $executableQueryObjectFactory;

	/** @var \SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryObjectFactoryInterface  */
	private $authenticatorQueryFactory;

	/** @var \SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface  */
	private $passwordHashStrategy;

	/**
	 * @param \SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface         $executableQueryObjectFactory
	 * @param \SixtyEightPublishers\User\Authentication\Query\AuthenticatorQueryObjectFactoryInterface $authenticatorQueryFactory
	 * @param \SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface     $passwordHashStrategy
	 */
	public function __construct(ExecutableQueryObjectFactoryInterface $executableQueryObjectFactory, AuthenticatorQueryObjectFactoryInterface $authenticatorQueryFactory, PasswordHashStrategyInterface $passwordHashStrategy)
	{
		$this->executableQueryObjectFactory = $executableQueryObjectFactory;
		$this->authenticatorQueryFactory = $authenticatorQueryFactory;
		$this->passwordHashStrategy = $passwordHashStrategy;
	}

	/**
	 * {@inheritdoc}
	 */
	public function authenticate(array $credentials): IIdentity
	{
		[ $login, $password ] = $this->validateCredentials($credentials);
		$user = $this->findUser($login);

		if (FALSE === $this->passwordHashStrategy->verify($password, $user->getPassword())) {
			throw new AuthenticationException(sprintf(
				'Invalid password for user "%s"',
				$login
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
				'Missing login field in credentials (key %s)',
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
	 * @param string $login
	 *
	 * @return \SixtyEightPublishers\User\Authentication\Entity\UserInterface
	 * @throws \Nette\Security\AuthenticationException
	 */
	private function findUser(string $login): UserInterface
	{
		try {
			$user = $this->executableQueryObjectFactory->create($this->authenticatorQueryFactory->create($login))->fetchOne();
		} catch (NonUniqueResultException $e) {
			$e = new AuthenticationException(sprintf(
				'User\'s login field is not unique! Value was "%s"',
				$login
			), self::FAILURE, $e);
		} catch (DBALException $e) {
			$e = new AuthenticationException(sprintf(
				'DBAL throws unexpected exception, login values was "%s"',
				$login
			), self::FAILURE, $e);
		}

		if (isset($e)) {
			throw $e;
		}

		if (!isset($user)) {
			throw new AuthenticationException(sprintf(
				'User "%s" not found.',
				$login
			), self::IDENTITY_NOT_FOUND);
		}

		return $user;
	}
}
