<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Translator;

use Nette;

trait TPrefixedTranslatorAware
{
	use TTranslatorAware;

	/** @var NULL|\Nette\Localization\ITranslator  */
	private $prefixedTranslator;

	/**
	 * @return \Nette\Localization\ITranslator
	 */
	public function getPrefixedTranslator(): Nette\Localization\ITranslator
	{
		if (NULL === $this->prefixedTranslator) {
			$this->prefixedTranslator = PrefixedTranslator::createFromClassName($this->getTranslator(), static::class);
		}

		return $this->prefixedTranslator;
	}
}
