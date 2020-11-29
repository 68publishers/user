<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use Nette\SmartObject;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\User\Common\DbalType\Password\Password;
use SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface;
use SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException;
use SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface;
use SixtyEightPublishers\User\ForgotPassword\Query\FindPasswordRequestByIdsQueryObjectFactoryInterface;

class PasswordRequestManager implements PasswordRequestManagerInterface
{
	use SmartObject;

	/** @var \SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface  */
	private $transactionFactory;

	/** @var \SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface  */
	private $passwordHashStrategy;

	/** @var \SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface  */
	private $executableQueryObjectFactory;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Query\FindPasswordRequestByIdsQueryObjectFactoryInterface  */
	private $findPasswordRequestByIdsQueryFactory;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface                               $transactionFactory
	 * @param \SixtyEightPublishers\User\Common\PasswordHashStrategy\PasswordHashStrategyInterface                $passwordHashStrategy
	 * @param \SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface                    $executableQueryObjectFactory
	 * @param \SixtyEightPublishers\User\ForgotPassword\Query\FindPasswordRequestByIdsQueryObjectFactoryInterface $findPasswordRequestByIdsQueryFactory
	 */
	public function __construct(TransactionFactoryInterface $transactionFactory, PasswordHashStrategyInterface $passwordHashStrategy, ExecutableQueryObjectFactoryInterface $executableQueryObjectFactory, FindPasswordRequestByIdsQueryObjectFactoryInterface $findPasswordRequestByIdsQueryFactory)
	{
		$this->transactionFactory = $transactionFactory;
		$this->passwordHashStrategy = $passwordHashStrategy;
		$this->executableQueryObjectFactory = $executableQueryObjectFactory;
		$this->findPasswordRequestByIdsQueryFactory = $findPasswordRequestByIdsQueryFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function findRequest($uid, $rid): PasswordRequestInterface
	{
		$request = $this->executableQueryObjectFactory->create($this->findPasswordRequestByIdsQueryFactory->create($uid, $rid))->fetchOne();

		if (!$request instanceof PasswordRequestInterface) {
			throw PasswordRequestProcessException::missingRequest($uid, $rid);
		}

		if (TRUE === $request->isExpired()) {
			throw PasswordRequestProcessException::expiredRequest($request->getUser()->getId(), $request->getId());
		}

		return $request;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Throwable
	 */
	public function reset(PasswordRequestInterface $passwordRequest, string $password): void
	{
		$transaction = $this->transactionFactory->create(function (EntityManagerInterface $em, PasswordRequestInterface $passwordRequest, string $password) {
			$user = $passwordRequest->getUser();

			if ($this->passwordHashStrategy->needRehash($password)) {
				$password = $this->passwordHashStrategy->hash($password);
			}

			$passwordRequest->getResetDeviceInfo()->fill();
			$passwordRequest->setStatus($passwordRequest::STATUS_COMPLETED);
			$user->setPassword(new Password($password));

			$em->persist($passwordRequest);
			$em->persist($user);

			return $user;
		});

		$transaction->error(static function (ErrorContextInterface $context) use ($passwordRequest) {
			$e = $context->getError();

			if (!$e instanceof PasswordRequestProcessException) {
				throw new PasswordRequestProcessException($passwordRequest->getUser()->getId(), $passwordRequest->getId(), $e->getMessage(), 0, $e);
			}
		});

		$transaction->withArguments([
			'passwordRequest' => $passwordRequest,
			'password' => $password,
		])->run();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Throwable
	 */
	public function cancel(PasswordRequestInterface $passwordRequest): void
	{
		$transaction = $this->transactionFactory->create(static function (EntityManagerInterface $em, PasswordRequestInterface $passwordRequest) {
			$passwordRequest->getResetDeviceInfo()->fill();
			$passwordRequest->setStatus($passwordRequest::STATUS_CANCELED);

			$em->persist($passwordRequest);

			return $passwordRequest;
		});

		$transaction->error(static function (ErrorContextInterface $context) use ($passwordRequest) {
			$e = $context->getError();

			if (!$e instanceof PasswordRequestProcessException) {
				throw new PasswordRequestProcessException($passwordRequest->getUser()->getId(), $passwordRequest->getId(), $e->getMessage(), 0, $e);
			}
		});

		$transaction->withArguments(['passwordRequest' => $passwordRequest])->run();
	}
}
