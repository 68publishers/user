<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common;

use ArrayAccess;
use SixtyEightPublishers\User\Common\Exception\RuntimeException;
use SixtyEightPublishers\User\Common\Exception\InvalidArgumentException;

final class UserMapping implements ArrayAccess
{
	public const FIELD_ID = 'id';
	public const FILED_EMAIL = 'email';
	public const FIELD_PASSWORD = 'password';
	public const FIELD_USERNAME = 'username';

	/** @var string  */
	private $className;

	/** @var array  */
	private $fields = [
		self::FIELD_ID => 'id',
		self::FILED_EMAIL => 'email',
		self::FIELD_PASSWORD => 'password',
		self::FIELD_USERNAME => 'username',
	];

	/**
	 * @param string $className
	 * @param array  $fields
	 */
	public function __construct(string $className, array $fields)
	{
		$this->className = $className;
		$this->fields = array_merge($this->fields, $fields);
	}

	/**
	 * @return string
	 */
	public function getClassName(): string
	{
		return $this->className;
	}

	/**
	 * @param mixed $name
	 *
	 * @return bool
	 */
	public function __isset($name): bool
	{
		return isset($this->fields[$name]);
	}

	/**
	 * @param mixed $name
	 *
	 * @return string
	 */
	public function __get($name): string
	{
		if (!$this->__isset($name)) {
			throw new InvalidArgumentException(sprintf(
				'Missing field with name "%s"',
				$name
			));
		}

		return $this->fields[$name];
	}

	/**
	 * @param mixed $name
	 * @param mixed $value
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\User\Common\Exception\RuntimeException
	 */
	public function __set($name, $value): void
	{
		throw new RuntimeException(sprintf(
			'Calling of method %s is not supported.',
			__METHOD__
		));
	}

	/**
	 * @param mixed $name
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\User\Common\Exception\RuntimeException
	 */
	public function __unset($name): void
	{
		throw new RuntimeException(sprintf(
			'Calling of method %s is not supported.',
			__METHOD__
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($offset): bool
	{
		return $this->__isset($offset);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($offset): string
	{
		return $this->__get($offset);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet($offset, $value): void
	{
		$this->__set($offset, $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset($offset): void
	{
		$this->__unset($offset);
	}
}
