<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Tests\Authentication\Authenticator;

use Tester\Assert;
use Nette\Security\IIdentity;
use Nette\Security\Authenticator;
use Nette\Security\SimpleIdentity;
use Nette\Security\IdentityHandler;
use Nette\Security\AuthenticationException;
use SixtyEightPublishers\User\Authentication\Authenticator\AuthenticatorMount;

require __DIR__ . '/../../bootstrap.php';

function createAuthenticator(string $name): Authenticator
{
	return new class($name) implements Authenticator {
		public function __construct(
			private readonly string $name
		) {
		}

		public function authenticate(string $username, string $password): IIdentity
		{
			return new SimpleIdentity($this->name . ':' . $username . ':' . $password);
		}
	};
}

function createMount(): AuthenticatorMount
{
	return new AuthenticatorMount(
		[
			'' => createAuthenticator('default'),
			'admin' => createAuthenticator('admin'),
		],
		new class implements IdentityHandler {
			public function sleepIdentity(IIdentity $identity): IIdentity
			{
				return $identity;
			}

			public function wakeupIdentity(IIdentity $identity): ?IIdentity
			{
				return $identity;
			}
		}
	);
}

test('A username without a prefix is authenticated by the default authenticator', static function (): void {
	Assert::same('default:john:secret', createMount()->authenticate('john', 'secret')->getId());
});

test('A prefixed username is authenticated by the authenticator with the prefix name', static function (): void {
	Assert::same('admin:john://x:secret', createMount()->authenticate('admin://john://x', 'secret')->getId());
});

test('An authentication exception is thrown for an unknown prefix', static function (): void {
	Assert::exception(
		static fn () => createMount()->authenticate('missing://john', 'secret'),
		AuthenticationException::class,
		'Missing Authenticator with name "missing"',
		Authenticator::Failure
	);
});
