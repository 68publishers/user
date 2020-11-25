<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Entity;

use SixtyEightPublishers\User\Common\Entity\Accessor\PasswordAccessorInterface;
use SixtyEightPublishers\User\Common\Entity\Accessor\UsernameAccessorInterface;
use SixtyEightPublishers\User\Common\Entity\UserInterface as CommonUserInterface;

interface UserInterface extends CommonUserInterface, UsernameAccessorInterface, PasswordAccessorInterface
{
}
