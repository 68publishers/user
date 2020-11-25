<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\DoctrineIdentity\Exception;

use BadMethodCallException;
use SixtyEightPublishers\User\Common\Exception\ExceptionInterface;

final class UnimplementedMethodException extends BadMethodCallException implements ExceptionInterface
{
	/**
	 * @param string $className
	 * @param string $method
	 *
	 * @return \SixtyEightPublishers\User\DoctrineIdentity\Exception\UnimplementedMethodException
	 */
	public static function unimplementedMethod(string $className, string $method): self
	{
		return new static(sprintf(
			'Called method %s::%s() is not implemented.',
			$className,
			$method
		));
	}
}
