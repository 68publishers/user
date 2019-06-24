<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Mail;

use Nette;

final class NullMailSender implements IMailSender
{
	use Nette\SmartObject;

	/*********** interface \SixtyEightPublishers\User\Common\Mail\IMailSender ***********/

	/**
	 * {@inheritdoc}
	 */
	public function send(string $mailName, array $to, array $args): void
	{
	}
}
