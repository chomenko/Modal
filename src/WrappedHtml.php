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
			"class" =>	"modal fade",
			"tabindex" => "-1",
			"role" => "dialog",
			"aria-labelledby" => "exampleModalLabel",
			"aria-hidden" => "true"
		]);
		return $this;
	}

	/**
	 * @return Html
	 */
	protected function createDialog()
	{
		return Html::el("div", [
			"class" =>	"modal-dialog",
			"role" => "document"
		]);
	}

	/**
	 * @return Html
	 */
	protected function createContent()
	{
		return Html::el("div", [
			"class" =>	"modal-content"
		]);
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
