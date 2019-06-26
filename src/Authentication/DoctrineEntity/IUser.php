<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\DoctrineEntity;

use SixtyEightPublishers;

interface IUser extends
	SixtyEightPublishers\User\Common\DoctrineEntity\IUser,
	SixtyEightPublishers\User\Common\DoctrineEntity\Accessor\IUsernameAccessor,
	SixtyEightPublishers\User\Common\DoctrineEntity\Accessor\IPasswordAccessor
{
}
