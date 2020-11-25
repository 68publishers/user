<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DoctrineIdentity\DI;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Security\IUserStorage;
use SixtyEightPublishers\User\DI\AbstractCompilerExtensionPass;
use SixtyEightPublishers\User\Common\Exception\RuntimeException;
use SixtyEightPublishers\User\DoctrineIdentity\UserStorageProxy;

final class DoctrineIdentityExtension extends AbstractCompilerExtensionPass
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
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'enabled' => Expect::bool(FALSE),
			'namespace' => Expect::string()->nullable()->dynamic(),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix('user_storage'))
			->setType(IUserStorage::class)
			->setFactory(UserStorageProxy::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$userStorageProxy = $builder->getDefinition($this->prefix('user_storage'));

		foreach ($builder->findByType(IUserStorage::class) as $name => $userStorage) {
			if ($name !== $this->prefix('user_storage') && TRUE === $userStorage->getAutowired()) {
				break;
			}
		}

		if (!isset($userStorage)) {
			throw new RuntimeException(sprintf(
				'Autowired service of type %s not found.',
				IUserStorage::class
			));
		}

		$userStorage->setAutowired(FALSE);

		$userStorageProxy->setAutowired(TRUE);
		$userStorageProxy->setArguments([
			'userStorage' => $userStorage,
		]);

		if (NULL !== $this->config->namespace) {
			$userStorageProxy->addSetup('$service->setNamespace(?)', [
				'namespace' => $this->config->namespace,
			]);
		}
	}
}
