<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Translator;

use Nette;

final class PrefixedTranslator implements Nette\Localization\ITranslator
{
	use Nette\SmartObject;

	/** @var \Nette\Localization\ITranslator  */
	private $translator;

	/** @var string  */
	private $prefix;

	/**
	 * @param \Nette\Localization\ITranslator $translator
	 * @param string                          $prefix
	 */
	public function __construct(Nette\Localization\ITranslator $translator, string $prefix)
	{
		$this->translator = $translator;
		$this->prefix = rtrim($prefix, '.');
	}
	
	/**
	 * @param \Nette\Localization\ITranslator $translator
	 * @param string                          $className
	 *
	 * @return \SixtyEightPublishers\User\Common\Translator\PrefixedTranslator
	 */
	public static function createFromClassName(Nette\Localization\ITranslator $translator, string $className): self
	{
		return new static(
			$translator,
			str_replace('\\', '_', $className)
		);
	}
	
	/*********** interface \Nette\Localization\ITranslator ***********/

	/**
	 * {@inheritdoc}
	 */
	public function translate($message, $count = NULL): string
	{
		return $this->translator->translate($this->prefix . '.' . $message, $count);
	}
}
