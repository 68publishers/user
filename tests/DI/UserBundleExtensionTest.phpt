<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Tests\DI;

use Tester\Assert;
use Nette\DI\Compiler;
use Nette\DI\Container;
use ReflectionProperty;
use Nette\DI\ContainerLoader;
use Nette\Security\Authenticator;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\Bridges\HttpDI\SessionExtension;
use Nette\Bridges\SecurityDI\SecurityExtension;
use SixtyEightPublishers\User\DI\UserBundleExtension;
use SixtyEightPublishers\User\Common\DbalType\Password\Password;
use SixtyEightPublishers\User\Common\DbalType\Password\PasswordType;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequest;
use SixtyEightPublishers\User\Authentication\Csrf\CsrfTokenFactoryInterface;
use SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface;
use SixtyEightPublishers\User\Authentication\Control\SignIn\SignInControlFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControlFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\Control\ForgotPassword\ForgotPasswordControlFactoryInterface;

require __DIR__ . '/../bootstrap.php';

function createContainer(): Container
{
	$loader = new ContainerLoader(TEMP_PATH . '/container', TRUE);
	$class = $loader->load(static function (Compiler $compiler): void {
		$compiler->addExtension('http', new HttpExtension(TRUE));
		$compiler->addExtension('session', new SessionExtension(FALSE, TRUE));
		$compiler->addExtension('security', new SecurityExtension(FALSE));
		$compiler->addExtension('user', new UserBundleExtension());
		$compiler->addConfig(['parameters' => ['tempDir' => TEMP_PATH]]);
		$compiler->loadConfig(__DIR__ . '/config.neon');
	});

	$container = new $class();
	assert($container instanceof Container);
	$container->initialize();

	return $container;
}

test('Container with a typical application configuration', static function (): void {
	$container = createContainer();

	Assert::same(['user.authentication.authenticator'], $container->findByType(Authenticator::class));
	Assert::true($container->hasService('nette.authenticator'));
	Assert::type(SignInControlFactoryInterface::class, $container->getByType(SignInControlFactoryInterface::class));
	Assert::type(ForgotPasswordControlFactoryInterface::class, $container->getByType(ForgotPasswordControlFactoryInterface::class));
	Assert::type(ResetPasswordControlFactoryInterface::class, $container->getByType(ResetPasswordControlFactoryInterface::class));
	Assert::type('string', $container->getByType(CsrfTokenFactoryInterface::class)->create('Test'));

	# set in the container's initialization
	Assert::same('2 hours', (new ReflectionProperty(PasswordRequest::class, 'expiration'))->getValue());
});

test('Passwords are hashed with the configured strategy', static function (): void {
	PasswordType::setPasswordHashStrategy(createContainer()->getByType(PasswordHashStrategyInterface::class));
	$password = new Password('secret');

	Assert::true($password->verify('secret'));
	Assert::false($password->verify('Secret'));
});
