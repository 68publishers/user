<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\UI;

use Nette;
use SixtyEightPublishers;

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
 * @method Nette\Security\User getUser()
 * @method mixed getParameter($key)
 * @method
 */
trait TLogoutPresenter
{
	/** @var string  */
	protected $tokenName = '_sec';

	/** @var string  */
	protected $tokenComponent = __CLASS__;

	/** @var NULL|\SixtyEightPublishers\User\Authentication\Csrf\ICsrfTokenFactory */
	private $csrfTokenFactory;

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
	 */
	protected function triggerUserNotLoggedIn(): void
	{
		throw new Nette\Application\ForbiddenRequestException();
	}

	/**
	 * Use can override the default behavior
	 *
	 * @return void
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	protected function triggerInvalidToken(): void
	{
		throw new Nette\Application\ForbiddenRequestException();
	}

	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\User\Authentication\Csrf\ICsrfTokenFactory $csrfTokenFactory
	 *
	 * @return void
	 */
	public function injectCsrfTokenFactory(SixtyEightPublishers\User\Authentication\Csrf\ICsrfTokenFactory $csrfTokenFactory): void
	{
		$this->csrfTokenFactory = $csrfTokenFactory;
	}

	/**
	 * {@inheritdoc}
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

		throw new SixtyEightPublishers\User\Common\Exception\RuntimeException(sprintf(
			'Method %s::triggerLoggedOut() must redirects when user is logged in.',
			__CLASS__
		));
	}
}
