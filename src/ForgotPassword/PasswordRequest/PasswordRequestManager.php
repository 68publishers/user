<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use Nette;
use Doctrine;
use SixtyEightPublishers;

class PasswordRequestManager implements IPasswordRequestManager
{
	use Nette\SmartObject;

	/** @var \Doctrine\ORM\EntityManagerInterface  */
	private $em;

	/** @var \SixtyEightPublishers\User\Common\PasswordHashStrategy\IPasswordHashStrategy  */
	private $passwordHashStrategy;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Query\IFindPasswordRequestByIdsQueryFactory  */
	private $findPasswordRequestByIdsQueryFactory;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                                  $em
	 * @param \SixtyEightPublishers\User\Common\PasswordHashStrategy\IPasswordHashStrategy          $passwordHashStrategy
	 * @param \SixtyEightPublishers\User\ForgotPassword\Query\IFindPasswordRequestByIdsQueryFactory $findPasswordRequestByIdsQueryFactory
	 */
	public function __construct(
		Doctrine\ORM\EntityManagerInterface $em,
		SixtyEightPublishers\User\Common\PasswordHashStrategy\IPasswordHashStrategy $passwordHashStrategy,
		SixtyEightPublishers\User\ForgotPassword\Query\IFindPasswordRequestByIdsQueryFactory $findPasswordRequestByIdsQueryFactory
	) {
		$this->em = $em;
		$this->passwordHashStrategy = $passwordHashStrategy;
		$this->findPasswordRequestByIdsQueryFactory = $findPasswordRequestByIdsQueryFactory;
	}

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request
	 * @param callable                                                                  $try
	 *
	 * @return mixed
	 * @throws \SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException
	 */
	private function tryCatch(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request, callable $try)
	{
		try {
			return $try($request);
		} catch (\Throwable $e) {
			if (!$e instanceof SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException) {
				$e = new SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException($request->getUser()->getId(), $request->getId(), $e->getMessage(), 0, $e);
			}

			throw $e;
		}
	}

	/*********** interface \SixtyEightPublishers\User\ForgotPassword\IPasswordRequestSender ***********/

	/**
	 * {@inheritdoc}
	 */
	public function findRequest($uid, $rid) : SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest
	{
		/** @var \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $request */
		$request = $this->findPasswordRequestByIdsQueryFactory
			->create($this->em, $uid, $rid)
			->getOneOrNullResult();

		if (NULL === $request) {
			throw SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException::missingRequest($uid, $rid);
		}

		if (TRUE === $request->isExpired()) {
			throw SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException::expiredRequest($request->getUser()->getId(), $request->getId());
		}

		return $request;
	}

	/**
	 * {@inheritdoc}
	 */
	public function reset(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest, string $password) : void
	{
		$this->tryCatch($passwordRequest, function (SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest) use ($password) {
			$user = $passwordRequest->getUser();

			if ($this->passwordHashStrategy->needRehash($password)) {
				$password = $this->passwordHashStrategy->hash($password);
			}

			$passwordRequest->getResetDeviceInfo()->fill();
			$passwordRequest->setStatus($passwordRequest::STATUS_COMPLETED);
			$user->setPassword($password);

			$this->em->persist($passwordRequest);
			$this->em->persist($user);
			$this->em->flush();
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function cancel(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest) : void
	{
		$this->tryCatch($passwordRequest, function (SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest $passwordRequest) {
			$passwordRequest->getResetDeviceInfo()->fill();
			$passwordRequest->setStatus($passwordRequest::STATUS_CANCELED);

			$this->em->persist($passwordRequest);
			$this->em->flush();
		});
	}
}
