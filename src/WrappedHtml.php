<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 16.12.2018
 */

namespace Chomenko\Modal;

use Nette\Utils\Html;

class WrappedHtml extends Html
{

	/**
	 * @var Html
	 */
	protected $wrapped;

	/**
	 * @var Html
	 */
	protected $dialog;

	/**
	 * @var Html
	 */
	protected $content;

	public function __construct()
	{
		$this->wrapped = $this->createWrapped();
		$this->dialog = $this->createDialog();
		$this->content = $this->createContent();

		$this->wrapped->addHtml($this->dialog);
		$this->dialog->addHtml($this->content);
	}

	/**
	 * @return Html
	 */
	protected function createWrapped()
	{
		$this->setName("div");
		$this->addAttributes([
			"class" => "modal fade",
			"tabindex" => "-1",
			"role" => "dialog",
			"aria-labelledby" => "exampleModalLabel",
			"aria-hidden" => "true",
		]);
		return $this;
	}

	/**
	 * @return Html
	 */
	protected function createDialog()
	{
		return Html::el("div", [
			"class" => "modal-dialog",
			"role" => "document",
		]);
	}

	/**
	 * @return Html
	 */
	protected function createContent()
	{
		return Html::el("div", [
			"class" => "modal-content",
		]);
	}

	/**
	 * @param string $title
	 * @return ModalHtml
	 */
	public function createHeader(string $title): ModalHtml
	{
		$header = ModalHtml::el("div", [
			"class" => "modal-header",
		]);

		$closeButton = Html::el("button", [
			"type" => "button",
			"class" => "close",
			"data-dismiss" => "modal",
			"aria-label" => "Close",
		])->setHtml(Html::el("span", [
			"aria-hidden" => "true",
		])->setText("×"));

		$label = Html::el("h4", [
			"class" => "modal-title",
		])->setHtml($title);

		$header->addHtml($closeButton)->addHtml($label);
		return $header;
	}

	/**
	 * @return ModalHtml
	 */
	public function createBody(): ModalHtml
	{
		return ModalHtml::el("div", [
			"class" => "modal-body",
		]);
	}

	/**
	 * @return ModalHtml
	 */
	public function createFooter(): ModalHtml
	{
		$closeButton = Html::el("button", [
			"type" => "button",
			"class" => "btn btn-default",
			"data-dismiss" => "modal",
		])->setText("Close");

		return ModalHtml::el("div", [
			"class" => "modal-footer",
		])->addHtml($closeButton);
	}

	/**
	 * @return Html
	 */
	public function getWrapped(): Html
	{
		return $this->wrapped;
	}

	/**
	 * @return Html
	 */
	public function getDialog(): Html
	{
		return $this->dialog;
	}

	/**
	 * @return Html
	 */
	public function getContent(): Html
	{
		return $this->content;
	}

}
