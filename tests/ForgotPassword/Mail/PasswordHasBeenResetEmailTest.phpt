<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Tests\ForgotPassword\Mail;

use Tester\Assert;
use SixtyEightPublishers\User\Tests\Fixtures\User;
use SixtyEightPublishers\User\Common\Mail\MailSenderInterface;
use SixtyEightPublishers\User\ForgotPassword\Mail\PasswordHasBeenResetEmail;

require __DIR__ . '/../../bootstrap.php';

test('The user is passed into the template as `userEntity`', static function (): void {
	$sender = new class implements MailSenderInterface {
		public array $sent = [];

		public function send(string $mailName, array $to, array $args): void
		{
			$this->sent[] = [$mailName, $to, $args];
		}
	};
	$user = new User();

	(new PasswordHasBeenResetEmail($sender))->send($user);

	Assert::count(1, $sender->sent);
	[$mailName, $to, $args] = $sender->sent[0];

	Assert::same(PasswordHasBeenResetEmail::NAME, $mailName);
	Assert::same('john@example.com', $to[0]->email);
	Assert::same(['userEntity' => $user], $args);
});
