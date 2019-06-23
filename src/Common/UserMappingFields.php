<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common;

use SixtyEightPublishers;

final class UserMappingFields implements \ArrayAccess
{
	const 	FIELD_ID = 'id',
			FILED_EMAIL = 'email',
			FIELD_PASSWORD = 'password',
			FIELD_LOGIN = 'login';

	/** @var array  */
	private $fields = [
		self::FIELD_ID => 'id',
		self::FILED_EMAIL => 'email',
		self::FIELD_PASSWORD => 'password',
		self::FIELD_LOGIN => 'login',
	];

	/**
	 * @param array $fields
	 */
	public function __construct(array $fields)
	{
		$this->fields = array_merge($this->fields, $fields);
	}

	/**
	 * @param mixed $name
	 *
	 * @return bool
	 */
	public function __isset($name) : bool
	{
		return isset($this->fields[$name]);
	}

	/**
	 * @param mixed $name
	 *
	 * @return string
	 */
	public function __get($name) : string
	{
		if (!$this->__isset($name)) {
			throw new SixtyEightPublishers\User\Common\Exception\InvalidArgumentException(sprintf(
				'Missing field with name "%s"',
				$name
			));
		}

		return $this->fields[$name];
	}

	/**************** interface \ArrayAccess ****************/

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($offset) : bool
	{
		return $this->__isset($offset);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($offset) : string
	{
		return $this->__get($offset);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet($offset, $value)
	{
		throw new SixtyEightPublishers\User\Common\Exception\RuntimeException(sprintf(
			'Calling of method %s is not supported.',
			__METHOD__
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset($offset)
	{
		throw new SixtyEightPublishers\User\Common\Exception\RuntimeException(sprintf(
			'Calling of method %s is not supported.',
			__METHOD__
		));
	}
}
