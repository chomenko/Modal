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

	public function render($indent = NULL)
	{
		echo parent::render($indent);
	}

}
