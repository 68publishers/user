<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use SixtyEightPublishers\User\Common\UserMapping;
use SixtyEightPublishers\User\Common\Entity\UserInterface;
use SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface;

final class HashUserPasswordSubscriber implements EventSubscriber
{
	/** @var \SixtyEightPublishers\User\Common\UserMapping  */
	private $userMapping;

	/** @var \SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface  */
	private $passwordHashStrategy;

	/**
	 * @param \SixtyEightPublishers\User\Common\UserMapping                                        $userMapping
	 * @param \SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface $passwordHashStrategy
	 */
	public function __construct(UserMapping $userMapping, PasswordHashStrategyInterface $passwordHashStrategy)
	{
		$this->userMapping = $userMapping;
		$this->passwordHashStrategy = $passwordHashStrategy;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSubscribedEvents(): array
	{
		return [
			Events::onFlush,
		];
	}

	/**
	 * @param \Doctrine\ORM\Event\OnFlushEventArgs $args
	 *
	 * @return void
	 */
	public function onFlush(OnFlushEventArgs $args): void
	{
		$em = $args->getEntityManager();
		$uow = $em->getUnitOfWork();
		$classMetadata = $em->getClassMetadata(UserInterface::class);

		/**
		 * @param array $objects
		 *
		 * @return \SixtyEightPublishers\User\Common\Entity\UserInterface[]
		 */
		$filter = static function (array $objects) {
			return array_filter($objects, static function ($object) {
				return $object instanceof UserInterface;
			});
		};

		foreach ($filter($uow->getScheduledEntityInsertions()) as $user) {
			$password = $classMetadata->getFieldValue($user, $this->userMapping[UserMapping::FIELD_PASSWORD]);

			if (!$this->passwordHashStrategy->needRehash($password)) {
				continue;
			}

			$classMetadata->setFieldValue($user, $this->userMapping[UserMapping::FIELD_PASSWORD], $this->passwordHashStrategy->hash($password));
		}

		foreach ($filter($uow->getScheduledEntityUpdates()) as $user) {
			$password = $classMetadata->getFieldValue($user, $this->userMapping[UserMapping::FIELD_PASSWORD]);

			if (!$this->passwordHashStrategy->needRehash($password)) {
				continue;
			}

			$classMetadata->setFieldValue($user, $this->userMapping[UserMapping::FIELD_PASSWORD], $this->passwordHashStrategy->hash($password));
		}
	}
}
