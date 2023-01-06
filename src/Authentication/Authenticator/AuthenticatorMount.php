<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Authenticator;

use Nette\Utils\Strings;
use Nette\Security\IIdentity;
use Nette\Security\IdentityHandler;
use Nette\Security\Authenticator as AuthenticatorInterface;
use Nette\Security\AuthenticationException;
use SixtyEightPublishers\User\Common\Exception\InvalidArgumentException;

final class AuthenticatorMount implements AuthenticatorInterface, IdentityHandler
{
	public const SEPARATOR = '://';

	/** @var array<AuthenticatorInterface> */
	private array $authenticators = [];

	/**
	 * @param array<AuthenticatorInterface> $authenticators
	 */
	public function __construct(
		array $authenticators,
		private readonly IdentityHandler $identityHandler
	) {
		foreach ($authenticators as $name => $authenticator) {
			$this->addAuthenticator((string) $name, $authenticator);
		}
	}

	public function authenticate(string $username, string $password): IIdentity
	{
		[ $prefix, $username ] = $this->getPrefixAndUsername($username);

		try {
			$authenticator = $this->getAuthenticator($prefix);
		} catch (InvalidArgumentException $e) {
			throw new AuthenticationException($e->getMessage(), self::FAILURE, $e);
		}

		return $authenticator->authenticate($username, $password);
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
	 * @throws \SixtyEightPublishers\User\Common\Exception\InvalidArgumentException
	 */
	public function getAuthenticator(string $name): AuthenticatorInterface
	{
		if (!isset($this->authenticators[$name])) {
			throw new InvalidArgumentException(sprintf(
				'Missing Authenticator with name "%s"',
				$name
			));
		}

		return $this->authenticators[$name];
	}

	private function addAuthenticator(string $name, AuthenticatorInterface $authenticator): void
	{
		$this->authenticators[$name] = $authenticator;
	}

	private function getPrefixAndUsername(string $username): array
	{
		if (Strings::contains($username, self::SEPARATOR)) {
			return explode(self::SEPARATOR, $username, 2);
		}

		return [ '', $username ];
	}
}
