<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\DoctrineEntity;

use Nette;

interface IUser extends Nette\Security\IIdentity
{
	/**
	 * {@inheritdoc}
	 */
	public function getRoles(): array;
}
