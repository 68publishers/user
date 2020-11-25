<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Authenticator;

use Nette\SmartObject;
use Nette\Utils\Strings;
use Nette\Security\IIdentity;
use Nette\Security\IAuthenticator;
use Nette\Security\AuthenticationException;
use SixtyEightPublishers\User\Common\Exception\InvalidArgumentException;

final class AuthenticatorMount implements IAuthenticator
{
	use SmartObject;

	public const SEPARATOR = '://';

	/** @var \Nette\Security\IAuthenticator[] */
	private $authenticators;

	/**
	 * @param \Nette\Security\IAuthenticator[] $authenticators
	 */
	public function __construct(array $authenticators)
	{
		foreach ($authenticators as $name => $authenticator) {
			$this->addAuthenticator((string) $name, $authenticator);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function authenticate(array $credentials): IIdentity
	{
		if (!isset($credentials[self::USERNAME])) {
			throw new AuthenticationException(sprintf(
				'Missing login field in credentials (key %s)',
				self::USERNAME
			), self::FAILURE);
		}

		[ $prefix, $login ] = $this->getPrefixAndLogin($credentials[self::USERNAME]);

		try {
			$authenticator = $this->getAuthenticator($prefix);
		} catch (InvalidArgumentException $e) {
			throw new AuthenticationException($e->getMessage(), self::FAILURE, $e);
		}

		$credentials[self::USERNAME] = $login;

		return $authenticator->authenticate($credentials);
	}

	/**
	 * @param string $name
	 *
	 * @return \Nette\Security\IAuthenticator
	 * @throws \SixtyEightPublishers\User\Common\Exception\InvalidArgumentException
	 */
	public function getAuthenticator(string $name): IAuthenticator
	{
		if (!isset($this->authenticators[$name])) {
			throw new InvalidArgumentException(sprintf(
				'Missing Authenticator with name "%s"',
				$name
			));
		}

		return $this->authenticators[$name];
	}

	/**
	 * @param string                         $name
	 * @param \Nette\Security\IAuthenticator $authenticator
	 *
	 * @return void
	 */
	private function addAuthenticator(string $name, IAuthenticator $authenticator): void
	{
		$this->authenticators[$name] = $authenticator;
	}

	/**
	 * @param string $login
	 *
	 * @return array
	 */
	private function getPrefixAndLogin(string $login): array
	{
		if (Strings::contains($login, self::SEPARATOR)) {
			return explode(self::SEPARATOR, $login, 2);
		}

		return [ '', $login ];
	}
}
