<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\DI;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpLiteral;
use SixtyEightPublishers\User\Common\DI\CommonExtension;
use SixtyEightPublishers\User\DI\AbstractCompilerExtensionPass;
use SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequest;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\TargetEntity;
use SixtyEightPublishers\User\Common\Exception\ConfigurationException;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\EntityMapping;
use SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordResetEmail;
use SixtyEightPublishers\User\ForgotPassword\Mail\PasswordHasBeenResetEmail;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\User\ForgotPassword\Query\GetUserByEmailQueryObject;
use SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordNotRegisteredEmail;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestSender;
use SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordResetEmailInterface;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestFactory;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManager;
use SixtyEightPublishers\User\ForgotPassword\Mail\PasswordHasBeenResetEmailInterface;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\TargetEntityProviderInterface;
use SixtyEightPublishers\User\ForgotPassword\Query\FindPasswordRequestByIdsQueryObject;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\EntityMappingProviderInterface;
use SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControl;
use SixtyEightPublishers\TranslationBridge\Bridge\Nette\DI\TranslationProviderInterface;
use SixtyEightPublishers\User\ForgotPassword\Control\ForgotPassword\ForgotPasswordControl;
use SixtyEightPublishers\User\ForgotPassword\Query\CancelPasswordRequestsByUserQueryObject;
use SixtyEightPublishers\User\ForgotPassword\Mail\ForgotPasswordNotRegisteredEmailInterface;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestSenderInterface;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\PasswordRequest\PasswordRequestManagerInterface;
use SixtyEightPublishers\User\ForgotPassword\Query\GetUserByEmailQueryObjectFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\Query\FindPasswordRequestByIdsQueryObjectFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\Control\ResetPassword\ResetPasswordControlFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\Control\ForgotPassword\ForgotPasswordControlFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\Query\CancelPasswordRequestsByUserQueryObjectFactoryInterface;

final class ForgotPasswordExtension extends AbstractCompilerExtensionPass implements EntityMappingProviderInterface, TargetEntityProviderInterface, TranslationProviderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function startup(): void
	{
		parent::startup();

		if (!$this->config->enabled) {
			$this->stopPropagation();
		}

		if (!is_subclass_of($this->sharedData[CommonExtension::SHARED_DATA_USER_CLASS_NAME], UserInterface::class, TRUE)) {
			throw new ConfigurationException(sprintf(
				'Your User entity must implement interface %s',
				UserInterface::class
			));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'enabled' => Expect::bool(FALSE),
			'register_controls' => Expect::bool(FALSE),
			'request_expiration' => Expect::anyOf(Expect::string(), Expect::int())->default(PasswordRequest::DEFAULT_EXPIRATION),
			'send_email_for_not_registered_users' => Expect::bool(TRUE),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('password_request_factory'))
			->setType(PasswordRequestFactoryInterface::class)
			->setFactory(PasswordRequestFactory::class);

		$builder->addDefinition($this->prefix('password_request_manager'))
			->setType(PasswordRequestManagerInterface::class)
			->setFactory(PasswordRequestManager::class);

		$builder->addDefinition($this->prefix('password_request_sender'))
			->setType(PasswordRequestSenderInterface::class)
			->setFactory(PasswordRequestSender::class, [
				'sendEmailForNotRegisteredUsers' => $this->config->send_email_for_not_registered_users,
			]);

		if ($this->config->register_controls) {
			$builder->addFactoryDefinition($this->prefix('control.forgot_password'))
				->setImplement(ForgotPasswordControlFactoryInterface::class)
				->getResultDefinition()
				->setFactory(ForgotPasswordControl::class);

			$builder->addFactoryDefinition($this->prefix('control.reset_password'))
				->setImplement(ResetPasswordControlFactoryInterface::class)
				->getResultDefinition()
				->setFactory(ResetPasswordControl::class);
		}

		$builder->addDefinition($this->prefix('email.forgot_password_not_registered'))
			->setType(ForgotPasswordNotRegisteredEmailInterface::class)
			->setFactory(ForgotPasswordNotRegisteredEmail::class);

		$builder->addDefinition($this->prefix('email.forgot_password_reset'))
			->setType(ForgotPasswordResetEmailInterface::class)
			->setFactory(ForgotPasswordResetEmail::class);

		$builder->addDefinition($this->prefix('email.password_has_been_reset'))
			->setType(PasswordHasBeenResetEmailInterface::class)
			->setFactory(PasswordHasBeenResetEmail::class);

		$builder->addFactoryDefinition($this->prefix('query_object_factory.cancel_password_request_by_user'))
			->setImplement(CancelPasswordRequestsByUserQueryObjectFactoryInterface::class)
			->getResultDefinition()
			->setFactory(CancelPasswordRequestsByUserQueryObject::class);

		$builder->addFactoryDefinition($this->prefix('query_object_factory.find_password_request_by_ids'))
			->setImplement(FindPasswordRequestByIdsQueryObjectFactoryInterface::class)
			->getResultDefinition()
			->setFactory(FindPasswordRequestByIdsQueryObject::class);

		$builder->addFactoryDefinition($this->prefix('query_object_factory.get_user_by_email'))
			->setImplement(GetUserByEmailQueryObjectFactoryInterface::class)
			->getResultDefinition()
			->setFactory(GetUserByEmailQueryObject::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterCompile(ClassType $class): void
	{
		$this->getInitialization()->addBody('?::setExpirationString(?);', [
			new PhpLiteral(PasswordRequest::class),
			(string) $this->config->request_expiration,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntityMappings(): array
	{
		return [
			new EntityMapping(EntityMapping::DRIVER_ANNOTATION, 'SixtyEightPublishers\User\ForgotPassword\Entity', __DIR__ . '/../Entity'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTargetEntities(): array
	{
		return [
			new TargetEntity(PasswordRequestInterface::class, PasswordRequest::class),
			new TargetEntity(UserInterface::class, $this->sharedData[CommonExtension::SHARED_DATA_USER_CLASS_NAME]),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTranslationResources(): array
	{
		return $this->config->register_controls ? [ __DIR__ . '/../translations' ] : [];
	}
}
