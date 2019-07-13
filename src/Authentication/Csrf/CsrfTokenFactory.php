<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Authentication\Csrf;

use Nette;

final class CsrfTokenFactory implements ICsrfTokenFactory
{
	use Nette\SmartObject;

	/** @var \Nette\Http\Session  */
	private $session;

	/**
	 * @param \Nette\Http\Session $session
	 */
	public function __construct(Nette\Http\Session $session)
	{
		$this->session = $session;
	}

	/************** interface \SixtyEightPublishers\User\Authentication\Csrf\ICsrfTokenFactory **************/

	/**
	 * {@inheritdoc}
	 */
	public function create(string $component = ''): string
	{
		$section = $this->session->getSection(__CLASS__);

		if (!isset($section['token'])) {
			$section['token'] = Nette\Utils\Random::generate(10);
		}

		$hash = hash_hmac('sha1', $component . $this->session->getId(), $section['token'], TRUE);

		return str_replace('/', '_', substr(base64_encode($hash), 0, 8));
	}
}
