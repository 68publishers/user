<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Mail;

use Nette\SmartObject;

final class NullMailSender implements MailSenderInterface
{
	use SmartObject;

	/**
	 * {@inheritdoc}
	 */
	public function send(string $mailName, array $to, array $args): void
	{
	}
}
