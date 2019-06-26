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
		'namespace' => NULL,
	];

	/**
	 * {@inheritdoc}
	 */
	protected function processConfig(array $config, \ArrayObject $sharedData): array
	{
		Nette\Utils\Validators::assertField($config, 'enabled', 'bool');
		Nette\Utils\Validators::assertField($config, 'namespace', 'null|string|' . Nette\DI\Statement::class);
		
		if (FALSE === $config['enabled']) {
			$this->stopPropagation();
		}

		return $config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix('user_storage'))
			->setType(Nette\Security\IUserStorage::class)
			->setFactory(SixtyEightPublishers\User\DoctrineIdentity\UserStorageProxy::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();
		$userStorageProxy = $builder->getDefinition($this->prefix('user_storage'));

		foreach ($builder->findByType(Nette\Security\IUserStorage::class) as $name => $userStorage) {
			if ($name !== $this->prefix('user_storage') && TRUE === $userStorage->isAutowired()) {
				break;
			}
		}

		if (!isset($userStorage)) {
			throw new SixtyEightPublishers\User\Common\Exception\RuntimeException(sprintf(
				'Autowired service of type %s not found.',
				Nette\Security\IUserStorage::class
			));
		}

		$userStorage->setAutowired(FALSE);

		$userStorageProxy->setAutowired(TRUE);
		$userStorageProxy->setArguments([
				'userStorage' => $userStorage,
		]);
		
		if (NULL !== $config['namespace']) {
			$userStorageProxy->addSetup('$service->setNamespace(?)', [
				'namespace' => $config['namespace'],
			]);
		}
	}
}
