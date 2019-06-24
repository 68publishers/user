<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Mail;

interface IMailSender
{
	/**
	 * @param string                                           $mailName
	 * @param \SixtyEightPublishers\User\Common\Mail\Address[] $to
	 * @param array                                            $args
	 *
	 * @return void
	 */
	public function send(string $mailName, array $to, array $args): void;
}
