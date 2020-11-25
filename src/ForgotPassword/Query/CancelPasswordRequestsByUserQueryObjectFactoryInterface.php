<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface;
use SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface;

interface CancelPasswordRequestsByUserQueryObjectFactoryInterface
{
	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface $user
	 *
	 * @return \SixtyEightPublishers\DoctrineQueryObjects\QueryObjectInterface
	 */
	public function create(UserInterface $user): QueryObjectInterface;
}
