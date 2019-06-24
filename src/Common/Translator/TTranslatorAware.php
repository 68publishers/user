<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Translator;

use Nette;

trait TTranslatorAware
{
	/** @var NULL|\Nette\Localization\ITranslator */
	private $translator;

	/**
	 * @param \Nette\Localization\ITranslator $translator
	 *
	 * @return void
	 */
	public function setTranslator(Nette\Localization\ITranslator $translator): void
	{
		$this->translator = $translator;
	}

	/**
	 * @return \Nette\Localization\ITranslator
	 */
	public function getTranslator(): Nette\Localization\ITranslator
	{
		if (NULL === $this->translator) {
			$this->translator = new NullTranslator();
		}

		return $this->translator;
	}
}
