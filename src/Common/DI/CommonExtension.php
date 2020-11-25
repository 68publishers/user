<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DI;

use ArrayObject;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use SixtyEightPublishers\User\Common\UserMapping;
use SixtyEightPublishers\DoctrineBridge\DI\TargetEntity;
use SixtyEightPublishers\User\Common\Logger\TracyLogger;
use SixtyEightPublishers\User\Common\Mail\NullMailSender;
use SixtyEightPublishers\User\Common\Entity\UserInterface;
use SixtyEightPublishers\User\Common\Logger\LoggerInterface;
use SixtyEightPublishers\User\Common\Mail\MailSenderInterface;
use SixtyEightPublishers\User\DI\AbstractCompilerExtensionPass;
use SixtyEightPublishers\User\Common\Exception\ConfigurationException;
use SixtyEightPublishers\DoctrineBridge\DI\TargetEntityProviderInterface;
use SixtyEightPublishers\User\Common\PasswordHashStrategy\DefaultPasswordHashStrategy;
use SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface;

final class CommonExtension extends AbstractCompilerExtensionPass implements TargetEntityProviderInterface
{
	public const SHARED_DATA_USER_CLASS_NAME = 'common.user_class_name';

	/**
	 * {@inheritDoc}
	 */
	public function attach(CompilerExtension $extension, ArrayObject $sharedData): void
	{
		parent::attach($extension, $sharedData);

		if (!is_subclass_of($this->config->user->class, UserInterface::class, TRUE)) {
			throw new ConfigurationException(sprintf(
				'Required setting %s.user.class must be valid classname of your User\'s entity that implements interface %s',
				$this->name,
				UserInterface::class
			));
		}

		$sharedData[self::SHARED_DATA_USER_CLASS_NAME] = $this->config->user->class;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'user' => Expect::structure([
				'class' => Expect::string()->required(),
				'fields' => Expect::array(),
			]),
			'password_strategy' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))
				->default(DefaultPasswordHashStrategy::class)
				->before(static function ($def) {
					return $def instanceof Statement ? $def : new Statement($def);
				}),
			'logger' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))
				->default(TracyLogger::class)
				->before(static function ($def) {
					return $def instanceof Statement ? $def : new Statement($def);
				}),
			'mail_sender' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))
				->default(NullMailSender::class)
				->before(static function ($def) {
					return $def instanceof Statement ? $def : new Statement($def);
				}),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('password_strategy'))
			->setType(PasswordHashStrategyInterface::class)
			->setFactory($this->config->password_strategy);

		$builder->addDefinition($this->prefix('logger'))
			->setType(LoggerInterface::class)
			->setFactory($this->config->logger);

		$builder->addDefinition($this->prefix('mail_sender'))
			->setType(MailSenderInterface::class)
			->setFactory($this->config->mail_sender);

		$fields = $this->config->user->fields;

		foreach ($fields as $k => $v) {
			if (defined($k)) {
				unset($fields[$k]);
				$fields[constant($k)] = $v;
			}
		}

		$builder->addDefinition($this->prefix('user_mapping'))
			->setType(UserMapping::class)
			->setArguments([$this->config->user->class, $fields]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTargetEntities(): array
	{
		return [
			new TargetEntity(UserInterface::class, $this->sharedData[self::SHARED_DATA_USER_CLASS_NAME]),
		];
	}
}
