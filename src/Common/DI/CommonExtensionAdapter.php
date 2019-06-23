<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DI;

use Nette;
use SixtyEightPublishers;

final class CommonExtensionAdapter extends SixtyEightPublishers\User\DI\AbstractExtensionAdapter
{
	/** @var array  */
	protected static $defaults = [
		'password_strategy' => SixtyEightPublishers\User\Common\PasswordHashStrategy\DefaultPasswordHashStrategy::class,
		'user_mapping_fields' => [],
		'logger' => SixtyEightPublishers\User\Common\Logger\TracyLogger::class,
		'mail_sender' => SixtyEightPublishers\User\Common\Mail\NullMailSender::class,
	];

	/**
	 * {@inheritdoc}
	 */
	protected function processConfig(array $config) : array
	{
		Nette\Utils\Validators::assertField($config, 'password_strategy', 'string|' . Nette\DI\Statement::class);
		Nette\Utils\Validators::assertField($config, 'user_mapping_fields', 'array');
		Nette\Utils\Validators::assertField($config, 'logger', 'string|' . Nette\DI\Statement::class);
		Nette\Utils\Validators::assertField($config, 'mail_sender', 'string|' . Nette\DI\Statement::class);

		return $config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration() : void
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

		$fields = $config['user_mapping_fields'];

		foreach ($fields as $k => $v) {
			if (defined($k)) {
				unset($fields[$k]);
				$fields[constant($k)] = $v;
			}
		}

		$builder->addDefinition($this->prefix('user_mapping_fields'))
			->setType(SixtyEightPublishers\User\Common\UserMappingFields::class)
			->setArguments([
				'fields' => $fields,
			]);
	}

	/**
	 * @param mixed $what
	 *
	 * @return bool
	 */
	private function needRegister($what) : bool
	{
		return (!is_string($what) || !Nette\Utils\Strings::startsWith($what, '@'));
	}
}
