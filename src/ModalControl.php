<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 14.12.2018
 */

namespace Chomenko\Modal;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Utils\Html;

abstract class ModalControl extends Control implements IModalControl
{

	/**
	 * @var callable[]
	 */
	private $onAttached = [];

	/**
	 * @var array
	 */
	private $persistent = [];

	/**
	 * @param AccessAction $accessAction
	 * @return bool
	 */
	public function access(AccessAction $accessAction): bool
	{
		return $accessAction->getUser()->isLoggedIn();
	}

	/**
	 * @return string
	 */
	public function getTitle() :string
	{
		return "Modal title";
	}

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @param ModalHtml $header
	 */
	public function renderHeader(WrappedHtml $wrappedHtml, ModalHtml $header)
	{
		$header->render();
	}

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @param ModalHtml $body
	 * @return mixed
	 */
	public function renderBody(WrappedHtml $wrappedHtml, ModalHtml $body)
	{
		$body->render();
	}

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @param ModalHtml $footer
	 * @return mixed|void
	 */
	public function renderFooter(WrappedHtml $wrappedHtml, ModalHtml $footer)
	{
		$this->template->setFile(__DIR__ . "/template/footer.latte");
		$this->template->render();
	}

	/**
	 * @param array $params
	 */
	public function loadState(array $params)
	{
		$list = get_class_vars(get_class($this));
		foreach ($params as $key => $value) {
			if (array_key_exists($key, $list)) {
				$this->{$key} = $value;
			}
		}
		$this->params = $params;
	}

	/**
	 * @param array $params
	 */
	public function saveState(array &$params)
	{
		parent::saveState($params);
		foreach ($this->persistent as $key => $value) {
			if (!array_key_exists($key, $params)) {
				$params[$key] = $value;
			}
		}
	}

	/**
	 * @param callable $callable
	 * @return mixed|void
	 */
	public function onAttached(callable $callable)
	{
		$this->onAttached[] = $callable;
	}

	/**
	 * @param object $presenter
	 */
	public function attached($presenter)
	{
		if ($presenter instanceof Presenter) {
			$this->loadState($presenter->popGlobalParameters($this->getUniqueId()));
			$this->onAnchor($this);
			foreach ($this->onAttached as $callable) {
				call_user_func_array($callable, [$this]);
			}
		}
	}

	/**
	 * @param array $params
	 */
	public function setPersistent(array $params)
	{
		$this->persistent = $params;
	}

	/**
	 * @param object $component
	 * @param array $params
	 * @return null|string
	 */
	protected function renderComponent(object $component, array $params = []): ?string
	{
		$output = NULL;
		if (method_exists($component, "render")) {
			ob_start();
			$result = call_user_func_array([$component, "render"], $params);
			$output = ob_get_contents();
			if (empty($output) && !empty($result)) {
				$output = $result;
			}
			ob_end_clean();
		}
		return $output;
	}

}
