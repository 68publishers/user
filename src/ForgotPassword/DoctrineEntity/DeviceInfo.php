<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\DoctrineEntity;

use Nette;
use DeviceDetector;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class DeviceInfo
{
	use Nette\SmartObject;

	/**
	 * @ORM\Column(type="string", length=45)
	 *
	 * @var string
	 */
	private $ip = '';

	/**
	 * @ORM\Column(type="string", length=100)
	 *
	 * @var string
	 */
	private $os = '';

	/**
	 * @ORM\Column(type="string", length=100)
	 *
	 * @var string
	 */
	private $userAgent = '';

	/**
	 * @return void
	 */
	public function fill() : void
	{
		$this->ip = $_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);

		static $detector = NULL;
		if (NULL === $detector) {
			$detector = new DeviceDetector\DeviceDetector($_SERVER['HTTP_USER_AGENT']);
			$detector->parse();
		}
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
	public function getIp() : string
	{
		return $this->ip;
	}

	/**
	 * @return string
	 */
	public function getOs() : string
	{
		return $this->os;
	}

	/**
	 * @return string
	 */
	public function getUserAgent() : string
	{
		return $this->userAgent;
	}
}
