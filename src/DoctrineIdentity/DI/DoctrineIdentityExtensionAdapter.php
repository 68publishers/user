<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DoctrineIdentity\DI;

use Nette;
use SixtyEightPublishers;

final class DoctrineIdentityExtensionAdapter extends SixtyEightPublishers\User\DI\AbstractExtensionAdapter
{
	/** @var array  */
	protected static $defaults = [
		'enabled' => FALSE,
	];

	/**
	 * {@inheritdoc}
	 */
	protected function processConfig(array $config) : array
	{
		Nette\Utils\Validators::assertField($config, 'enabled', 'bool');

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
		$this->getContainerBuilder()
			->addDefinition($this->prefix('user_storage'))
			->setType(Nette\Security\IUserStorage::class)
			->setFactory(SixtyEightPublishers\User\DoctrineIdentity\UserStorageProxy::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile() : void
	{
		$builder = $this->getContainerBuilder();

		foreach ($builder->findByType(Nette\Security\IUserStorage::class) as $name => $userStorage) {
			if ($name !== $this->prefix('user_storage')) {
				break;
			}
		}

		if (!isset($userStorage)) {
			throw new SixtyEightPublishers\User\Common\Exception\RuntimeException(sprintf(
				'Service of type %s not found.',
				Nette\Security\IUserStorage::class
			));
		}

		$userStorage->setAutowired(FALSE);

		$builder->getDefinition($this->prefix('user_storage'))
			->setArguments([
				'userStorage' => $userStorage,
			])
			->setAutowired(TRUE);
	}
}
