<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Tests\ForgotPassword\UI;

use Tester\Assert;
use Nette\Application\UI\Presenter;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequestInterface;
use SixtyEightPublishers\User\ForgotPassword\UI\CancelPasswordRequestPresenterTrait;
use SixtyEightPublishers\User\ForgotPassword\Exception\PasswordRequestProcessException;

require __DIR__ . '/../../bootstrap.php';

final class CancelPasswordRequestPresenter extends Presenter
{
	use CancelPasswordRequestPresenterTrait;

	protected function triggerSuccess(PasswordRequestInterface $passwordRequest): void
	{
	}

	protected function triggerError(PasswordRequestProcessException $e): void
	{
	}
}

test('The action is always changed to `default` on startup', static function (): void {
	$presenter = new CancelPasswordRequestPresenter();
	$presenter->changeAction('foo');
	$presenter->startup();

	Assert::same('default', $presenter->getAction());
});
