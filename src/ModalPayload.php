<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Chomenko\Modal;

class ModalPayload extends \stdClass
{

	/**
	 * @var string
	 */
	public $modalId;

	/**
	 * @var bool
	 */
	public $close = FALSE;

	public function __construct($modalId)
	{
		$this->modalId = $modalId;
	}

	/**
	 * @return bool
	 */
	public function isClose(): bool
	{
		return $this->close;
	}

	/**
	 * @param bool $close
	 * @return $this
	 */
	public function setClose(bool $close)
	{
		$this->close = $close;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getModalId(): string
	{
		return $this->modalId;
	}

}
