<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\DoctrineEntity;

use SixtyEightPublishers;

interface IUser extends
	SixtyEightPublishers\User\Common\DoctrineEntity\IUser,
	SixtyEightPublishers\User\Common\DoctrineEntity\Accessor\IEmailAccessor,
	SixtyEightPublishers\User\Common\DoctrineEntity\Assigner\IPasswordAssigner
{
}
