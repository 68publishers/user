<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Entity;

use Doctrine\ORM\Mapping as ORM;
use DeviceDetector\DeviceDetector;

#[ORM\Embeddable]
final class DeviceInfo
{
	/** @var string */
	#[ORM\Column(type: 'string', length: 45)]
	private $ip = '';

	/** @var string */
	#[ORM\Column(type: 'string', length: 100)]
	private $os = '';

	/** @var string */
	#[ORM\Column(type: 'string', length: 100)]
	private $userAgent = '';

	/**
	 * @return void
	 */
	public function fill(): void
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
		$this->ip = trim(explode(',', $ip)[0]); # the first address of X-Forwarded-For is the client

		$detector = new DeviceDetector($_SERVER['HTTP_USER_AGENT'] ?? '');
		$detector->parse();
		$this->os = sprintf(
			'%s %s %s',
			$detector->getOs('name'),
			$detector->getOs('version'),
			$detector->getOs('platform')
		);
		$this->userAgent = sprintf(
			'%s - %s %s, engine: %s %s',
			$detector->getClient('type'),
			$detector->getClient('name'),
			$detector->getClient('version'),
			$detector->getClient('engine'),
			$detector->getClient('engine_version')
		);
	}

	/**
	 * @return string
	 */
	public function getIp(): string
	{
		return $this->ip;
	}

	/**
	 * @return string
	 */
	public function getOs(): string
	{
		return $this->os;
	}

	/**
	 * @return string
	 */
	public function getUserAgent(): string
	{
		return $this->userAgent;
	}
}
