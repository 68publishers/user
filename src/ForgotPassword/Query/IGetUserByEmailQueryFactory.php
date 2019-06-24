<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Query;

use Doctrine;

interface IGetUserByEmailQueryFactory
{
	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 * @param string                               $email
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function create(Doctrine\ORM\EntityManagerInterface $em, string $email): Doctrine\ORM\Query;
}
