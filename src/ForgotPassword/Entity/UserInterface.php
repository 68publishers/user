<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Entity;

use SixtyEightPublishers\User\Common\Entity\Accessor\EmailAccessorInterface;
use SixtyEightPublishers\User\Common\Entity\Assigner\PasswordAssignerInterface;
use SixtyEightPublishers\User\Common\Entity\UserInterface as CommonUserInterface;

interface UserInterface extends CommonUserInterface, EmailAccessorInterface, PasswordAssignerInterface
{
}
