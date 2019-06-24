<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DI;

use Kdyby;
use Nette;
use SixtyEightPublishers;

final class CommonExtensionAdapter extends SixtyEightPublishers\User\DI\AbstractExtensionAdapter implements Kdyby\Doctrine\DI\ITargetEntityProvider
{
	const 	SHARED_DATA_USER_CLASS_NAME = 'common.user_class_name';

	/** @var array  */
	protected static $defaults = [
		'user' => [
			'class' => '',
			'fields' => [],
		],
		'password_strategy' => SixtyEightPublishers\User\Common\PasswordHashStrategy\DefaultPasswordHashStrategy::class,
		'logger' => SixtyEightPublishers\User\Common\Logger\TracyLogger::class,
		'mail_sender' => SixtyEightPublishers\User\Common\Mail\NullMailSender::class,
	];

	/**
	 * {@inheritdoc}
	 */
	protected function processConfig(array $config, \ArrayObject $sharedData): array
	{
		Nette\Utils\Validators::assertField($config, 'user', 'array');
		Nette\Utils\Validators::assertField($config['user'], 'class', 'string');
		Nette\Utils\Validators::assertField($config['user'], 'fields', 'array');

		Nette\Utils\Validators::assertField($config, 'password_strategy', 'string|' . Nette\DI\Statement::class);
		Nette\Utils\Validators::assertField($config, 'logger', 'string|' . Nette\DI\Statement::class);
		Nette\Utils\Validators::assertField($config, 'mail_sender', 'string|' . Nette\DI\Statement::class);

		if (!is_subclass_of($config['user']['class'], SixtyEightPublishers\User\Common\DoctrineEntity\IUser::class, TRUE)) {
			throw new SixtyEightPublishers\User\Common\Exception\ConfigurationException(sprintf(
				'Required setting %s.user_class_name must be valid classname of your User\'s entity that implements interface %s',
				$this->name,
				SixtyEightPublishers\User\Common\DoctrineEntity\IUser::class
			));
		}

		$sharedData[self::SHARED_DATA_USER_CLASS_NAME] = $config['user']['class'];

		return $config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		if (TRUE === $this->needRegister($config['password_strategy'])) {
			$builder->addDefinition($this->prefix('password_strategy'))
				->setType(SixtyEightPublishers\User\Common\PasswordHashStrategy\IPasswordHashStrategy::class)
				->setFactory($config['password_strategy']);
		}

		if (TRUE === $this->needRegister($config['logger'])) {
			$builder->addDefinition($this->prefix('logger'))
				->setType(SixtyEightPublishers\User\Common\Logger\ILogger::class)
				->setFactory($config['logger']);
		}

		if (TRUE === $this->needRegister($config['mail_sender'])) {
			$builder->addDefinition($this->prefix('mail_sender'))
				->setType(SixtyEightPublishers\User\Common\Mail\IMailSender::class)
				->setFactory($config['mail_sender']);
		}

		$fields = $config['user']['fields'];

		foreach ($fields as $k => $v) {
			if (defined($k)) {
				unset($fields[$k]);
				$fields[constant($k)] = $v;
			}
		}

		$builder->addDefinition($this->prefix('user_mapping'))
			->setType(SixtyEightPublishers\User\Common\UserMapping::class)
			->setArguments([
				'className' => $config['user']['class'],
				'fields' => $fields,
			]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$translator = $builder->getByType(Nette\Localization\ITranslator::class, FALSE);

		if (NULL === $translator) {
			return;
		}

		$translator = $builder->getDefinition($translator);

		/** @var \Nette\DI\ServiceDefinition[] $translatableServices */
		$translatableServices = array_filter($builder->getDefinitions(), function (Nette\DI\ServiceDefinition $def) {
			return is_a($def->getImplement(), SixtyEightPublishers\User\Common\Translator\ITranslatableService::class, TRUE)
				|| ($def->getImplementMode() !== $def::IMPLEMENT_MODE_GET && is_a($def->getType(), SixtyEightPublishers\User\Common\Translator\ITranslatableService::class, TRUE));
		});

		foreach ($translatableServices as $translatableService) {
			$translatableService->addSetup('setTranslator', [
				'translator' => $translator,
			]);
		}
	}

	/**
	 * @param mixed $what
	 *
	 * @return bool
	 */
	private function needRegister($what): bool
	{
		return (!is_string($what) || !Nette\Utils\Strings::startsWith($what, '@'));
	}

	/**************** interface \Kdyby\Doctrine\DI\ITargetEntityProvider ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getTargetEntityMappings(): array
	{
		return [
			SixtyEightPublishers\User\Common\DoctrineEntity\IUser::class => $this->getSharedData(self::SHARED_DATA_USER_CLASS_NAME),
		];
	}
}
