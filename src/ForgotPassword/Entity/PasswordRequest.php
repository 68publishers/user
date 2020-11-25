<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\ForgotPassword\Entity;

use DateTime;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use SixtyEightPublishers\User\Common\Exception\InvalidArgumentException;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class PasswordRequest implements PasswordRequestInterface
{
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
	 * @ORM\ManyToOne(targetEntity="UserInterface")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 *
	 * @var \SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface
	 */
	private $user;

	/**
	 * @ORM\Embedded(class="DeviceInfo", columnPrefix="request_")
	 *
	 * @var \SixtyEightPublishers\User\ForgotPassword\Entity\DeviceInfo
	 */
	private $requestDeviceInfo;

	/**
	 * @ORM\Embedded(class="DeviceInfo", columnPrefix="reset_")
	 *
	 * @var \SixtyEightPublishers\User\ForgotPassword\Entity\DeviceInfo
	 */
	private $resetDeviceInfo;

	/** @var string  */
	private static $expiration = self::DEFAULT_EXPIRATION;

	/**
	 * @param \SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface $user
	 *
	 * @throws \Exception
	 */
	public function __construct(UserInterface $user)
	{
		$this->id = Uuid::uuid4();
		$this->user = $user;
		$this->status = self::STATUS_CREATED;
		$this->created = new DateTime('now', new DateTimeZone('UTC'));
		$this->updated = new DateTime('now', new DateTimeZone('UTC'));
		$this->requestDeviceInfo = new DeviceInfo();
		$this->resetDeviceInfo = new DeviceInfo();
	}

	/**
	 * @internal
	 * @ORM\PreUpdate
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function onPreUpdate(): void
	{
		$this->updated = new DateTime('now');
	}

	/**
	 * @param string $expiration
	 *
	 * @return void
	 */
	public static function setExpirationString(string $expiration): void
	{
		self::$expiration = $expiration;
	}

	/**
	 * @return \Ramsey\Uuid\UuidInterface
	 */
	public function getId(): UuidInterface
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getStatus(): string
	{
		return $this->status;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setStatus(string $status): void
	{
		if (!in_array($status, self::STATUSES, TRUE)) {
			throw new InvalidArgumentException(sprintf(
				'Value %s is not in allowed set [%s]',
				$status,
				implode(', ', array_map('strval', self::STATUSES))
			));
		}

		$this->status = $status;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCreated(): DateTime
	{
		return $this->created;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUpdated(): DateTime
	{
		return $this->updated;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUser(): UserInterface
	{
		return $this->user;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getExpiration(): DateTime
	{
		return (clone $this->created)->modify(self::$expiration);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Exception
	 */
	public function isExpired(): bool
	{
		return $this->getExpiration() < new DateTime('now', new DateTimeZone('UTC'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRequestDeviceInfo(): DeviceInfo
	{
		return $this->requestDeviceInfo;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResetDeviceInfo(): DeviceInfo
	{
		return $this->resetDeviceInfo;
	}
}
