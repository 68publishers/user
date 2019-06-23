<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use Doctrine;
use SixtyEightPublishers;

interface ICancelPasswordRequestsByUserQueryFactory
{
	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                           $em
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function create(Doctrine\ORM\EntityManagerInterface $em, SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user) : Doctrine\ORM\Query;
}
