<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\UI;

use Nette\Security\User;
use Nette\Application\ForbiddenRequestException;
use SixtyEightPublishers\User\Common\Exception\RuntimeException;
use SixtyEightPublishers\User\Authentication\Csrf\CsrfTokenFactoryInterface;

/**
 * Use in Presenter!
 *
 * For link creation use:
 *
 * <code>
 * $this->link(':My:Logout:Presenter', [
 *      '_sec' => $csrfTokenFactory->create(My\Logout\Presenter::class),
 * ]);
 * </code>
 *
 *
 * @method User getUser()
 * @method mixed getParameter($key)
 * @method
 */
trait LogoutPresenterTrait
{
	/** @var string  */
	protected $tokenName = '_sec';

	/** @var string  */
	protected $tokenComponent = __CLASS__;

	/** @var NULL|\SixtyEightPublishers\User\Authentication\Csrf\CsrfTokenFactoryInterface */
	private $csrfTokenFactory;

	/**
	 * @param \SixtyEightPublishers\User\Authentication\Csrf\CsrfTokenFactoryInterface $csrfTokenFactory
	 *
	 * @return void
	 *@internal
	 *
	 */
	public function injectCsrfTokenFactory(CsrfTokenFactoryInterface $csrfTokenFactory): void
	{
		$this->csrfTokenFactory = $csrfTokenFactory;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function startup(): void
	{
		/** @noinspection PhpUndefinedClassInspection */
		parent::startup();

		$user = $this->getUser();

		if (!$user->isLoggedIn()) {
			$this->triggerUserNotLoggedIn();
		}

		if ($this->getParameter($this->tokenName) !== $this->csrfTokenFactory->create($this->tokenComponent)) {
			$this->triggerInvalidToken();
		}

		$user->logout();
		$this->triggerLoggedOut();

		throw new RuntimeException(sprintf(
			'Method %s::triggerLoggedOut() must redirects when user is logged in.',
			__CLASS__
		));
	}

	/**
	 * Do redirect in this method, you can also add flash messages etc.
	 *
	 * @return void
	 */
	abstract protected function triggerLoggedOut(): void;

	/**
	 * Use can override the default behavior
	 *
	 * @return void
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	protected function triggerUserNotLoggedIn(): void
	{
		throw new ForbiddenRequestException('');
	}

	/**
	 * Use can override the default behavior
	 *
	 * @return void
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	protected function triggerInvalidToken(): void
	{
		throw new ForbiddenRequestException('');
	}
}
