<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 14.12.2018
 */

namespace Chomenko\Modal;

use Chomenko\AppWebLoader\AppWebLoader;
use Chomenko\AppWebLoader\Exceptions\AppWebLoaderException;
use Chomenko\Modal\DI\ModalExtension;
use Chomenko\Modal\Events\EventListener;
use Chomenko\Modal\Events\Subscriber;
use Chomenko\Modal\Exceptions\ModalException;
use Nette\Application\UI\BadSignalException;
use Nette\Application\UI\Control;
use Nette\Http\Url;
use Nette\Security\User;

class WrappedModal extends Control
{

	/**
	 * @var ModalController
	 */
	private $controller;

	/**
	 * @var ModalFactory
	 */
	private $modalFactory;

	/**
	 * @var EventListener
	 */
	private $eventListener;

	/**
	 * @var User
	 */
	private $user;

	public function __construct(
		AppWebLoader $appWebLoader,
		ModalController $controller,
		EventListener $eventListener,
		User $user
	) {
		$this->controller = $controller;
		$this->eventListener = $eventListener;
		$this->user = $user;
		try {
			$collection = $appWebLoader->createCollection("modal");
			$collection->addScript(__DIR__ . "/template/modal.js");
		}catch (AppWebLoaderException $e) {
			return;
		}
	}

	public function render()
	{
		if (!$this->modalFactory) {
			return;
		}

		$component = $this->modalFactory->getInstance();

		$factory = $this->modalFactory;
		$wrapped = new WrappedHtml();
		$wrapped->setAttribute("id", $factory->getId());

		$this->eventListener->emit(Subscriber::BEFORE_RENDER, $component, $factory, [$wrapped]);

		ob_start();
		$component->renderHeader($wrapped, $wrapped->createHeader($component->getTitle()));
		$component->renderBody($wrapped, $wrapped->createBody());
		$component->renderFooter($wrapped, $wrapped->createFooter());
		$output = ob_get_contents();
		ob_end_clean();

		$wrapped->getContent()->addHtml($output);
		$this->eventListener->emit(Subscriber::AFTER_RENDER, $component, $factory, [$wrapped]);

		$this->template->factory = $factory;
		$this->template->html = $wrapped;
		$this->template->setFile(__DIR__ . "/template/wrapped.latte");
		$this->template->render();
	}

	/**
	 * @param mixed $signal
	 * @throws BadSignalException
	 * @throws ModalException
	 * @throws \ReflectionException
	 */
	public function signalReceived($signal)
	{
		try {
			parent::signalReceived($signal);
		} catch ( BadSignalException $e) {

			$exp = explode("-", $signal);
			$factory = $this->controller->getById($exp[0]);
			if (!$factory) {
				$class = get_class($this);
				throw new BadSignalException("There is no handler for signal '$signal' in class $class.");
			}
			$this->createModal($factory);
		}
	}

	/**
	 * @param string $name
	 * @param bool $throw
	 * @return \Nette\ComponentModel\IComponent|null
	 * @throws ModalException
	 * @throws \ReflectionException
	 */
	public function getComponent($name, $throw = TRUE)
	{
		$exp = explode("-", $name);
		$factory = $this->controller->getById($exp[0]);
		if ($factory) {
			$this->createModal($factory);
		}
		return parent::getComponent($name, $throw);
	}

	/**
	 * @param ModalFactory $factory
	 * @return ModalFactory
	 * @throws ModalException
	 * @throws \ReflectionException
	 */
	private function createModal(ModalFactory $factory)
	{
		if (!$this->modalFactory) {
			$component = $factory->getInstance();
			$this->eventListener->emit(Subscriber::CREATE, $component, $factory, [$factory]);
			$this->accessor($component, $factory);


			if (method_exists($component, "create")) {
				$parameters = $this->getComponentParams($factory);
				$method = new \ReflectionMethod($component, "create");
				$arguments = [];
				foreach ($method->getParameters() as $parameter) {
					if (!$parameter->isOptional() && !array_key_exists($parameter->getName(), $parameters)) {
						throw ModalException::modalRequiredParameter(get_class($component), $parameter->getName());
					}
					if (array_key_exists($parameter->getName(), $parameters)) {
						$arguments[$parameter->getName()] = $parameters[$parameter->getName()];
					} else {
						$arguments[$parameter->getName()] = $parameter->getDefaultValue();
					}
				}
				$component->setPersistent($arguments);
				call_user_func_array([$component, "create"], $arguments);
			}

			$this->addComponent($component, $factory->getId());
			$this->modalFactory = $factory;
		}
		return $this->modalFactory;
	}

	/**
	 * @param ModalFactory $factory
	 * @return array
	 */
	private function getComponentParams(ModalFactory $factory) : array
	{
		$parameters = [];
		$actualLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url = new Url($actualLink);
		foreach ($url->getQueryParameters() as $key => $value) {
			$prefix = ModalExtension::CONTROL_NAME . "-" . $factory->getId() . "-";
			$len = strlen($prefix);
			if (substr($key, 0, $len) === $prefix) {
				$name = substr($key, $len, strlen($key));
				$parameters[$name] = $value;
			}
		}
		return $parameters;
	}

	/**
	 * @param ModalControl $control
	 * @param ModalFactory $factory
	 * @throws ModalException
	 */
	private function accessor(ModalControl $control, ModalFactory $factory)
	{
		$accessAction = new AccessAction($this->getPresenter(), $this->user);
		$parentAccess = $this->eventListener->emit(Subscriber::ACCESS, $control, $factory, [$accessAction]);

		if (is_bool($parentAccess)) {
			$accessAction->setAllowed($parentAccess);
		}

		$access = $control->access($accessAction);

		if (!$access) {
			$throw = $this->eventListener->emit(Subscriber::ACCESS_FAILURE, $control, $factory, [$accessAction]);
			if (!is_bool($throw) || ($throw === TRUE)) {
				throw ModalException::accessDenied();
			}
		}
		$this->eventListener->emit(Subscriber::ACCESS_SUCCESS, $control, $factory, [$accessAction]);
	}

}
