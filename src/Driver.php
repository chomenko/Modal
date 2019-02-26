<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Chomenko\Modal;

use Chomenko\Modal\Exceptions\ModalException;
use Nette\Application\UI\Presenter;

class Driver
{

	/**
	 * @var ModalFactory
	 */
	private $factory;

	/**
	 * @var WrappedModal
	 */
	private $modal;

	/**
	 * @var ModalPayload
	 */
	private $payload;

	/**
	 * @var Presenter
	 */
	private $presenter;

	/**
	 * @param ModalFactory $factory
	 */
	public function __construct(ModalFactory $factory)
	{
		$this->factory = $factory;
		$modalId = $this->factory->getId();
		$this->payload = new ModalPayload($modalId);
	}

	/**
	 * @param Presenter $presenter
	 */
	public function attach(Presenter $presenter)
	{
		$this->presenter = $presenter;
	}

	/**
	 * @param bool $close
	 * @throws ModalException
	 */
	public function closeModal(bool $close = TRUE)
	{
		$this->getPayload()->setClose($close);
	}

	/**
	 * @return Presenter
	 * @throws ModalException
	 */
	private function getPresenter(): Presenter
	{
		if (!$this->presenter instanceof Presenter) {
			throw ModalException::modalDriverIsNotAttached();
		}
		return $this->presenter;
	}

	/**
	 * @return ModalPayload
	 * @throws ModalException
	 */
	public function getPayload(): ModalPayload
	{
		$presenter = $this->getPresenter();
		if (!isset($presenter->payload->modal)) {
			$presenter->payload->modal = [];
		}
		$id = $this->factory->getId();
		if (!array_key_exists($id, $presenter->payload->modal)) {
			$presenter->payload->modal[$id] = $this->payload;
		}
		return $this->payload;
	}

}
