<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use Nette;
use Doctrine;
use PersistenceBundle;
use SixtyEightPublishers;

class PasswordRequestFactory implements IPasswordRequestFactory
{
	use Nette\SmartObject;

	/** @var \Doctrine\ORM\EntityManagerInterface  */
	private $em;

	/** @var \PersistenceBundle\Transaction\ITransactionFactory  */
	private $transactionFactory;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Query\IGetUserByEmailQueryFactory  */
	private $getUserByEmailQueryFactory;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Query\ICancelPasswordRequestsByUserQueryFactory  */
	private $cancelPasswordRequestsByUserQueryFactory;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                                      $em
	 * @param \PersistenceBundle\Transaction\ITransactionFactory                                        $transactionFactory
	 * @param \SixtyEightPublishers\User\ForgotPassword\Query\IGetUserByEmailQueryFactory               $getUserByEmailQueryFactory
	 * @param \SixtyEightPublishers\User\ForgotPassword\Query\ICancelPasswordRequestsByUserQueryFactory $cancelPasswordRequestsByUserQueryFactory
	 */
	public function __construct(
		Doctrine\ORM\EntityManagerInterface $em,
		PersistenceBundle\Transaction\ITransactionFactory $transactionFactory,
		SixtyEightPublishers\User\ForgotPassword\Query\IGetUserByEmailQueryFactory $getUserByEmailQueryFactory,
		SixtyEightPublishers\User\ForgotPassword\Query\ICancelPasswordRequestsByUserQueryFactory $cancelPasswordRequestsByUserQueryFactory
	) {
		$this->em = $em;
		$this->transactionFactory = $transactionFactory;
		$this->getUserByEmailQueryFactory = $getUserByEmailQueryFactory;
		$this->cancelPasswordRequestsByUserQueryFactory = $cancelPasswordRequestsByUserQueryFactory;
	}

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest
	 */
	protected function createPasswordRequestEntity(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user): SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest
	{
		return new SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\PasswordRequest($user);
	}

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest
	 */
	private function getRequest(SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user): SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest
	{
		$transaction = $this->transactionFactory->create(function (Doctrine\ORM\EntityManagerInterface $em, SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user) {
			$request = $this->createPasswordRequestEntity($user);

			$this->cancelPasswordRequestsByUserQueryFactory->create($em, $user)->execute();

			$request->getRequestDeviceInfo()->fill();
			$em->persist($request);

			return $request;
		});

		return $transaction->run($user);
	}

	/*********** interface \SixtyEightPublishers\User\ForgotPassword\IPasswordRequestFactory ***********/

	/**
	 * {@inheritdoc}
	 */
	public function create(string $email): SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest
	{
		try {
			/** @var NULL|\SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user */
			$user = $this->getUserByEmailQueryFactory->create($this->em, $email)->getOneOrNullResult();

			if (NULL === $user) {
				throw SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException::notRegisteredEmail($email);
			}

			return $this->getRequest($user);
		} catch (\Throwable $e) {
			if (!$e instanceof SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException) {
				$e = new SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException($e->getMessage(), $e->getCode(), $e);
			}

			throw $e;
		}
	}
}
