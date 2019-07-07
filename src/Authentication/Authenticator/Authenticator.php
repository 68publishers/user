<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Authenticator;

use Nette;
use Doctrine;
use SixtyEightPublishers;

final class Authenticator implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\User\Authentication\Query\IAuthenticatorQueryFactory  */
	private $authenticatorQueryFactory;

	/** @var \SixtyEightPublishers\User\Common\PasswordHashStrategy\IPasswordHashStrategy  */
	private $passwordHashStrategy;

	/**
	 * @param \SixtyEightPublishers\User\Authentication\Query\IAuthenticatorQueryFactory   $authenticatorQueryFactory
	 * @param \SixtyEightPublishers\User\Common\PasswordHashStrategy\IPasswordHashStrategy $passwordHashStrategy
	 */
	public function __construct(
		SixtyEightPublishers\User\Authentication\Query\IAuthenticatorQueryFactory $authenticatorQueryFactory,
		SixtyEightPublishers\User\Common\PasswordHashStrategy\IPasswordHashStrategy $passwordHashStrategy
	) {
		$this->authenticatorQueryFactory = $authenticatorQueryFactory;
		$this->passwordHashStrategy = $passwordHashStrategy;
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
			throw new Nette\Security\AuthenticationException(sprintf(
				'Missing login field in credentials (key %s)',
				self::USERNAME
			), self::FAILURE);
		}

		if (!isset($credentials[self::PASSWORD])) {
			throw new Nette\Security\AuthenticationException(sprintf(
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
	 * @return \SixtyEightPublishers\User\Authentication\DoctrineEntity\IUser
	 * @throws \Nette\Security\AuthenticationException
	 */
	private function findUser(string $login): SixtyEightPublishers\User\Authentication\DoctrineEntity\IUser
	{
		try {
			$user = $this->authenticatorQueryFactory
				->create($login)
				->getOneOrNullResult();
		} catch (Doctrine\ORM\NonUniqueResultException $e) {
			$e = new Nette\Security\AuthenticationException(sprintf(
				'User\'s login field is not unique! Value was "%s"',
				$login
			), self::FAILURE, $e);
		} catch (Doctrine\DBAL\DBALException $e) {
			$e = new Nette\Security\AuthenticationException(sprintf(
				'DBAL throws unexpected exception, login values was "%s"',
				$login
			), self::FAILURE, $e);
		}

		if (isset($e)) {
			throw $e;
		}

		if (!isset($user)) {
			throw new Nette\Security\AuthenticationException(sprintf(
				'User "%s" not found.',
				$login
			), self::IDENTITY_NOT_FOUND);
		}

		return $user;
	}

	/************* interface \Nette\Security\IAuthenticator *************/

	/**
	 * {@inheritdoc}
	 */
	public function authenticate(array $credentials): Nette\Security\IIdentity
	{
		[ $login, $password ] = $this->validateCredentials($credentials);
		$user = $this->findUser($login);

		if (FALSE === $this->passwordHashStrategy->verify($password, $user->getPassword())) {
			throw new Nette\Security\AuthenticationException(sprintf(
				'Invalid password for user "%s"',
				$login
			), self::INVALID_CREDENTIAL);
		}

		return $user;
	}
}
