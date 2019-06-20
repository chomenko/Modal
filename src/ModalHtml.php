<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 12.01.2019
 */

namespace Chomenko\Modal;

use Nette\Utils\Html;

class ModalHtml extends Html
{

	private $rendered = FALSE;

	public function render($indent = NULL)
	{
		$this->rendered = TRUE;
		echo parent::render($indent);
	}

	/**
	 * @return bool
	 */
	public function isRendered(): bool
	{
		return $this->rendered;
	}

}
