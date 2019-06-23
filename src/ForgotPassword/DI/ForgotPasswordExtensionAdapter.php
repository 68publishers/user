<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\DI;

use Kdyby;
use Nette;
use SixtyEightPublishers;

final class ForgotPasswordExtensionAdapter extends SixtyEightPublishers\User\DI\AbstractExtensionAdapter implements
	Kdyby\Doctrine\DI\IEntityProvider,
	Kdyby\Doctrine\DI\ITargetEntityProvider
{
	/** @var array  */
	protected static $defaults = [
		'enabled' => FALSE,
		'register_controls' => FALSE,
		'request_expiration' => SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\PasswordRequest::DEFAULT_EXPIRATION,
	];

	/**
	 * {@inheritdoc}
	 */
	protected function processConfig(array $config) : array
	{
		Nette\Utils\Validators::assertField($config, 'enabled', 'bool');
		Nette\Utils\Validators::assertField($config, 'register_controls', 'bool');
		Nette\Utils\Validators::assertField($config, 'request_expiration', 'string|int');

		if (FALSE === $config['enabled']) {
			$this->stopPropagation();
		}

		return $config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration() : void
	{
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		# services
		$builder->addDefinition($this->prefix('password_request_factory'))
			->setType(SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestFactory::class)
			->setFactory(SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestFactory::class);

		$builder->addDefinition($this->prefix('password_request_manager'))
			->setType(SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestManager::class)
			->setFactory(SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManager::class);

		$builder->addDefinition($this->prefix('password_request_sender'))
			->setType(SixtyEightPublishers\User\ForgotPassword\PasswordRequest\IPasswordRequestSender::class)
			->setFactory(SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestSender::class);

		# controls
		if (TRUE === $config['register_controls']) {
			$builder->addDefinition($this->prefix('control.forgot_password'))
				->setImplement(SixtyEightPublishers\User\ForgotPassword\Control\ForgotPassword\IForgotPasswordControlFactory::class)
				->setFactory(SixtyEightPublishers\User\ForgotPassword\Control\ForgotPassword\ForgotPasswordControl::class);

			$builder->addDefinition($this->prefix('control.reset_password'))
				->setImplement(SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\IResetPasswordControlFactory::class)
				->setFactory(SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControl::class);
		}

		# emails
		$builder->addDefinition($this->prefix('email.forgot_password_not_registered'))
			->setType(SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordNotRegisteredEmail::class)
			->setFactory(SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordNotRegisteredEmail::class);

		$builder->addDefinition($this->prefix('email.forgot_password_reset'))
			->setType(SixtyEightPublishers\User\ForgotPassword\Mail\IForgotPasswordResetEmail::class)
			->setFactory(SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordResetEmail::class);

		$builder->addDefinition($this->prefix('email.password_has_been_reset'))
			->setType(SixtyEightPublishers\User\ForgotPassword\Mail\IPasswordHasBeenResetEmail::class)
			->setFactory(SixtyEightPublishers\User\ForgotPassword\Mail\PasswordHasBeenResetEmail::class);

		# queries
		$builder->addDefinition($this->prefix('query_factory.cancel_password_request_by_user'))
			->setType(SixtyEightPublishers\User\ForgotPassword\Query\ICancelPasswordRequestsByUserQueryFactory::class)
			->setFactory(SixtyEightPublishers\User\ForgotPassword\Query\CancelPasswordRequestsByUserQueryFactory::class);

		$builder->addDefinition($this->prefix('query_factory.find_password_request_by_ids'))
			->setType(SixtyEightPublishers\User\ForgotPassword\Query\IFindPasswordRequestByIdsQueryFactory::class)
			->setFactory(SixtyEightPublishers\User\ForgotPassword\Query\FindPasswordRequestByIdsQueryFactory::class);

		$builder->addDefinition($this->prefix('query_factory.get_user_by_email'))
			->setType(SixtyEightPublishers\User\ForgotPassword\Query\IGetUserByEmailQueryFactory::class)
			->setFactory(SixtyEightPublishers\User\ForgotPassword\Query\GetUserByEmailQueryFactory::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class) : void
	{
		$config = $this->getConfig();
		$initialize = $class->getMethod('initialize');

		$initialize->addBody('?::setExpirationString(?);', [
			new Nette\PhpGenerator\PhpLiteral(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\PasswordRequest::class),
			(string) $config['request_expiration'],
		]);
	}

	/**************** interface \Kdyby\Doctrine\DI\IEntityProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getEntityMappings() : array
	{
		return [
			'SixtyEightPublishers\User\ForgotPassword\DoctrineEntity' => __DIR__ . '/../DoctrineEntity',
		];
	}

	/**************** interface \Kdyby\Doctrine\DI\ITargetEntityProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getTargetEntityMappings() : array
	{
		return [
			SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest::class => SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\PasswordRequest::class,
		];
	}
}
