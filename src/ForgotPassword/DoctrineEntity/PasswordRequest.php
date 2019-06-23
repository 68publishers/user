<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\DoctrineEntity;

use Nette;
use Ramsey;
use SixtyEightPublishers;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
final class PasswordRequest implements IPasswordRequest
{
	use Nette\SmartObject;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="uuid", unique=true)
	 *
	 * @var \Ramsey\Uuid\UuidInterface
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=15)
	 *
	 * @var string
	 */
	private $status;

	/**
	 * @ORM\Column(type="datetime")
	 *
	 * @var \DateTime
	 */
	private $created;

	/**
	 * @ORM\Column(type="datetime")
	 *
	 * @var \DateTime
	 */
	private $updated;

	/**
	 * @ORM\ManyToOne(targetEntity="IUser")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 *
	 * @var \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser
	 */
	private $user;

	/**
	 * @ORM\Embedded(class="DeviceInfo", columnPrefix="request_")
	 *
	 * @var \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\DeviceInfo
	 */
	private $requestDeviceInfo;

	/**
	 * @ORM\Embedded(class="DeviceInfo", columnPrefix="reset_")
	 *
	 * @var \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\DeviceInfo
	 */
	private $resetDeviceInfo;

	/** @var string  */
	private static $expiration = self::DEFAULT_EXPIRATION;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IUser $user
	 */
	public function __construct(IUser $user)
	{
		$this->id = Ramsey\Uuid\Uuid::uuid1();
		$this->user = $user;
		$this->status = self::STATUS_CREATED;
		$this->created = new \DateTime('now', new \DateTimeZone('UTC'));
		$this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
		$this->requestDeviceInfo = new DeviceInfo();
		$this->resetDeviceInfo = new DeviceInfo();
	}

	/**
	 * @internal
	 * @ORM\PreUpdate
	 *
	 * @return void
	 */
	public function onPreUpdate() : void
	{
		$this->updated = new \DateTime('now');
	}

	/**
	 * @param string $expiration
	 *
	 * @return void
	 */
	public static function setExpirationString(string $expiration) : void
	{
		self::$expiration = $expiration;
	}

	/**************** interface \SixtyEightPublishers\User\ForgotPassword\DoctrineEntity\IPasswordRequest ****************/

	/**
	 * @return \Ramsey\Uuid\UuidInterface
	 */
	public function getId() : Ramsey\Uuid\UuidInterface
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getStatus() : string
	{
		return $this->status;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setStatus(string $status) : void
	{
		if (!in_array($status, self::STATUSES)) {
			throw new SixtyEightPublishers\User\Common\Exception\InvalidArgumentException(sprintf(
				'Value %s is not in allowed set [%s]',
				(string) $status,
				implode(', ', array_map('strval', self::STATUSES))
			));
		}

		$this->status = $status;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCreated() : \DateTime
	{
		return $this->created;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUpdated() : \DateTime
	{
		return $this->updated;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUser() : IUser
	{
		return $this->user;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getExpiration() : \DateTime
	{
		return (clone $this->created)->modify(self::$expiration);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isExpired() : bool
	{
		return $this->getExpiration() < new \DateTime('now', new \DateTimeZone('UTC'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRequestDeviceInfo() : DeviceInfo
	{
		return $this->requestDeviceInfo;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResetDeviceInfo() : DeviceInfo
	{
		return $this->resetDeviceInfo;
	}
}
