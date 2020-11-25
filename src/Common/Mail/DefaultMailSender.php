<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Common\Mail;

use Nette\SmartObject;
use Contributte\Mailing\IMailBuilderFactory;
use SixtyEightPublishers\User\Common\Exception\MailSendingException;

final class DefaultMailSender implements MailSenderInterface
{
	use SmartObject;

	/** @var string  */
	private $templatesDir;

	/** @var \SixtyEightPublishers\User\Common\Mail\Address  */
	private $from;

	/** @var array  */
	private $subjects;

	/** @var \Contributte\Mailing\IMailBuilderFactory  */
	private $mailBuilderFactory;

	/**
	 * @param string                                         $templatesDir
	 * @param \SixtyEightPublishers\User\Common\Mail\Address $from
	 * @param array                                          $subjects
	 * @param \Contributte\Mailing\IMailBuilderFactory       $mailBuilderFactory
	 */
	public function __construct(string $templatesDir, Address $from, array $subjects, IMailBuilderFactory $mailBuilderFactory)
	{
		$this->templatesDir = rtrim($templatesDir, '\\/');
		$this->from = $from;
		$this->subjects = $subjects;
		$this->mailBuilderFactory = $mailBuilderFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function send(string $mailName, array $to, array $args): void
	{
		$file = $this->templatesDir . '/' . $mailName . '.latte';

		if (!file_exists($file)) {
			throw new MailSendingException(sprintf(
				'Missing template for mail "%s"',
				$mailName
			));
		}

		$builder = $this->mailBuilderFactory->create();

		$builder->getTemplate()->setFile($file);
		$builder->setParameters($args);
		$builder->setSubject($this->subjects[$mailName] ?? '');
		$builder->setFrom($this->from->getEmail(), $this->from->getName());

		/** @var \SixtyEightPublishers\User\Common\Mail\Address $address */
		foreach ($to as $address) {
			$builder->addTo($address->getEmail(), $address->getName());
		}

		$builder->send();
	}
}
