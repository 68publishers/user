<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Authenticator;

use Nette;
use SixtyEightPublishers;

final class AuthenticatorMount implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	const 	SEPARATOR = '://';

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
	 * @param string                         $name
	 * @param \Nette\Security\IAuthenticator $authenticator
	 *
	 * @return void
	 */
	private function addAuthenticator(string $name, Nette\Security\IAuthenticator $authenticator): void
	{
		$this->authenticators[$name] = $authenticator;
	}

	/**
	 * @param string $name
	 *
	 * @return \Nette\Security\IAuthenticator
	 * @throws \SixtyEightPublishers\User\Common\Exception\InvalidArgumentException
	 */
	public function getAuthenticator(string $name): Nette\Security\IAuthenticator
	{
		if (!isset($this->authenticators[$name])) {
			throw new SixtyEightPublishers\User\Common\Exception\InvalidArgumentException(sprintf(
				'Missing Authenticator with name "%s"',
				$name
			));
		}

		return $this->authenticators[$name];
	}

	/**
	 * @param string $login
	 *
	 * @return array
	 */
	private function getPrefixAndLogin(string $login): array
	{
		if (Nette\Utils\Strings::contains($login, self::SEPARATOR)) {
			return explode(self::SEPARATOR, $login, 2);
		}

		return [ '', $login ];
	}

	/************* interface \Nette\Security\IAuthenticator *************/

	/**
	 * {@inheritdoc}
	 */
	public function authenticate(array $credentials): Nette\Security\IIdentity
	{
		if (!isset($credentials[self::USERNAME])) {
			throw new Nette\Security\AuthenticationException(sprintf(
				'Missing login field in credentials (key %s)',
				self::USERNAME
			), self::FAILURE);
		}

		[ $prefix, $login ] = $this->getPrefixAndLogin($credentials[self::USERNAME]);

		try {
			$authenticator = $this->getAuthenticator($prefix);
		} catch (SixtyEightPublishers\User\Common\Exception\InvalidArgumentException $e) {
			throw new Nette\Security\AuthenticationException($e->getMessage(), self::FAILURE, $e);
		}

		$credentials[self::USERNAME] = $login;

		return $authenticator->authenticate($credentials);
	}
}
