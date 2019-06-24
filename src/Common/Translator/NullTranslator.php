<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Translator;

use Nette;

final class NullTranslator implements Nette\Localization\ITranslator
{
	use Nette\SmartObject;
	
	/*********** interface \Nette\Localization\ITranslator ***********/

	/**
	 * {@inheritdoc}
	 */
	public function translate($message, $count = NULL): string
	{
		return (string) $message;
	}
}
