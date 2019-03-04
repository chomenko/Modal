<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 16.12.2018
 */

namespace Chomenko\Modal\Tracy;

use Chomenko\Modal\ModalController;
use Nette\Utils\Html;
use Tracy\IBarPanel;
use Latte;

class Panel implements IBarPanel
{

	/**
	 * @var ModalController
	 */
	private $controller;

	/**
	 * @param ModalController $modalController
	 */
	public function __construct(ModalController $modalController)
	{
		$this->controller = $modalController;
	}

	/**
	 * @return Html
	 */
	private function getIconHtml()
	{
		$path = __DIR__ . '/icon.png';
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents(__DIR__ . '/icon.png');
		$src = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return Html::el("img")
			->setAttribute("src", $src)
			->setAttribute('weight', 16)
			->setAttribute('height', 16);
	}

	/**
	 * @return string
	 */
	public function getTab()
	{
		$count = count($this->controller->getModels());
		return (string)Html::el()->addHtml($this->getIconHtml())->addText(" " . $count);
	}

	/**
	 * @return string
	 */
	public function getPanel()
	{
		$latte = new Latte\Engine;
		$data = [
			"list" => $this->controller->getModels(),
		];
		return $latte->renderToString(__DIR__ . '/panel.latte', $data);
	}

}
