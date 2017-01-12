<?php

namespace SixtyEightPublishers\User\Model;

use Nette\SmartObject;

class User
{
	use SmartObject;

	/** @var null|string */
	private $login;

	/** @var null|string */
	private $password;

	/**
	 * @return null|string
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * @param null|string $login
	 * @return User
	 */
	public function setLogin($login)
	{
		$this->login = $login;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param null|string $password
	 * @return User
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}
}
