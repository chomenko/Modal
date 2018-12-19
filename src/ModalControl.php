<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 14.12.2018
 */

namespace Chomenko\Modal;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;

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
	protected function getTitle() :string
	{
		return "Modal title";
	}

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @return mixed|void
	 */
	public function renderHeader(WrappedHtml $wrappedHtml)
	{
		$this->template->title = $this->getTitle();
		$this->template->setFile(__DIR__ . "/template/header.latte");
		$this->template->render();
	}

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @return mixed
	 */
	public abstract function renderBody(WrappedHtml $wrappedHtml);

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @return mixed|void
	 */
	public function renderFooter(WrappedHtml $wrappedHtml)
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
		foreach ($params as $key => $value){
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
	 * @param $presenter
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

}
