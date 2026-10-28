<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Tests\ForgotPassword\Entity;

use Tester\Assert;
use SixtyEightPublishers\User\ForgotPassword\Entity\DeviceInfo;

require __DIR__ . '/../../bootstrap.php';

const CHROME_ON_LINUX = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
const SAFARI_ON_IPHONE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1';

function fill(array $server): DeviceInfo
{
	unset($_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
	$_SERVER = $server + $_SERVER;

	$deviceInfo = new DeviceInfo();
	$deviceInfo->fill();

	return $deviceInfo;
}

test('Without a User-Agent and a remote address (CLI)', static function (): void {
	$deviceInfo = fill([]);

	Assert::same('', $deviceInfo->getIp());
	Assert::type('string', $deviceInfo->getOs());
	Assert::type('string', $deviceInfo->getUserAgent());
});

test('The remote address and the User-Agent are used', static function (): void {
	$deviceInfo = fill(['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => CHROME_ON_LINUX]);

	Assert::same('127.0.0.1', $deviceInfo->getIp());
	Assert::contains('Linux', $deviceInfo->getOs());
	Assert::contains('Chrome', $deviceInfo->getUserAgent());
});

test('The first address of X-Forwarded-For is used', static function (): void {
	$deviceInfo = fill(['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_FORWARDED_FOR' => '203.0.113.7, 10.0.0.2, 10.0.0.3']);

	Assert::same('203.0.113.7', $deviceInfo->getIp());
});

test('Every call detects the current User-Agent', static function (): void {
	$chrome = fill(['HTTP_USER_AGENT' => CHROME_ON_LINUX]);
	$safari = fill(['HTTP_USER_AGENT' => SAFARI_ON_IPHONE]);

	Assert::contains('Chrome', $chrome->getUserAgent());
	Assert::contains('Safari', $safari->getUserAgent());
	Assert::contains('iOS', $safari->getOs());
});
