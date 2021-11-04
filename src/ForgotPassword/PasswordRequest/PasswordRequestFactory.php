<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\PasswordRequest;

use Nette\SmartObject;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequest;
use SixtyEightPublishers\DoctrinePersistence\Badge\NonLoggableErrorBadge;
use SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface;
use SixtyEightPublishers\DoctrineQueryObjects\ResultSet\ResultSetOptions;
use SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\DoctrinePersistence\Context\TransactionContextInterface;
use SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestCreationException;
use SixtyEightPublishers\User\ForgotPassword\Query\GetUserByEmailQueryObjectFactoryInterface;
use SixtyEightPublishers\User\ForgotPassword\Query\CancelPasswordRequestsByUserQueryObjectFactoryInterface;

class PasswordRequestFactory implements PasswordRequestFactoryInterface
{
	use SmartObject;

	/** @var \SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface  */
	private $transactionFactory;

	/** @var \SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface  */
	private $executableQueryObjectFactory;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Query\GetUserByEmailQueryObjectFactoryInterface  */
	private $getUserByEmailQueryFactory;

	/** @var \SixtyEightPublishers\User\ForgotPassword\Query\CancelPasswordRequestsByUserQueryObjectFactoryInterface  */
	private $cancelPasswordRequestsByUserQueryFactory;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface                                   $transactionFactory
	 * @param \SixtyEightPublishers\DoctrineQueryObjects\ExecutableQueryObjectFactoryInterface                        $executableQueryObjectFactory
	 * @param \SixtyEightPublishers\User\ForgotPassword\Query\GetUserByEmailQueryObjectFactoryInterface               $getUserByEmailQueryFactory
	 * @param \SixtyEightPublishers\User\ForgotPassword\Query\CancelPasswordRequestsByUserQueryObjectFactoryInterface $cancelPasswordRequestsByUserQueryFactory
	 */
	public function __construct(TransactionFactoryInterface $transactionFactory, ExecutableQueryObjectFactoryInterface $executableQueryObjectFactory, GetUserByEmailQueryObjectFactoryInterface $getUserByEmailQueryFactory, CancelPasswordRequestsByUserQueryObjectFactoryInterface $cancelPasswordRequestsByUserQueryFactory)
	{
		$this->transactionFactory = $transactionFactory;
		$this->executableQueryObjectFactory = $executableQueryObjectFactory;
		$this->getUserByEmailQueryFactory = $getUserByEmailQueryFactory;
		$this->cancelPasswordRequestsByUserQueryFactory = $cancelPasswordRequestsByUserQueryFactory;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Throwable
	 */
	public function create(string $email): PasswordRequestInterface
	{
		$transaction = $this->transactionFactory->create(function (TransactionContextInterface $context, string $email) {
			$user = $this->executableQueryObjectFactory
				->create($this->getUserByEmailQueryFactory->create($email))
				->fetchOne();

			if (NULL === $user) {
				$e = PasswordRequestCreationException::notRegisteredEmail($email);

				$context->addBadges(new NonLoggableErrorBadge($e));

				throw $e;
			}

			return $user;
		});

		$transaction->then(function (EntityManagerInterface $em, UserInterface $result) {
			$request = $this->createPasswordRequestEntity($result);

			$this->executableQueryObjectFactory->create(
				$this->cancelPasswordRequestsByUserQueryFactory->create($result),
				(new ResultSetOptions())->setHydrationMode(AbstractQuery::HYDRATE_SCALAR)
			)->fetch();

			$request->getRequestDeviceInfo()->fill();
			$em->persist($request);

			return $request;
		});

		$transaction->error(static function (ErrorContextInterface $context) {
			$e = $context->getError();

			if (!$e instanceof PasswordRequestCreationException) {
				throw new PasswordRequestCreationException($e->getMessage(), $e->getCode(), $e);
			}
		});

		return $transaction->withArguments(['email' => $email])->run();
	}

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface $user
	 *
	 * @return \SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface
	 * @throws \Exception
	 */
	protected function createPasswordRequestEntity(UserInterface $user): PasswordRequestInterface
	{
		return new PasswordRequest($user);
	}
}
