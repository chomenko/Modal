<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 16.12.2018
 */

namespace Chomenko\Modal;

use Chomenko\Modal\DI\ModalExtension;
use Nette\Http\Request;
use Nette\Http\Url;
use Nette\Utils\Html;

class ModalFactory
{

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $interface;

	/**
	 * @var object
	 */
	private $service;

	/**
	 * @var ModalControl|null
	 */
	private $instance;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Url
	 */
	private $url;

	/**
	 * @var Driver
	 */
	private $driver;

	/**
	 * @var bool
	 */
	private $active = FALSE;

	/**
	 * @var string
	 */
	private $className;

	/**
	 * @param string $interface
	 * @param string $className
	 * @param object $service
	 * @param Request $request
	 */
	public function __construct(string $interface, string $className, object $service, Request $request)
	{
		$this->id = hash("crc32b", $interface);
		$this->interface = $interface;
		$this->service = $service;
		$this->request = $request;
		$this->className = $className;
		$this->url = $this->createUrl($request);
		$this->driver = new Driver($this);
	}

	/**
	 * @return Url
	 */
	private function createUrl()
	{
		$originalUrl = $this->request->getUrl();
		$modalUrl = clone $originalUrl;

		if ($modalUrl->getQueryParameter("do", NULL)) {
			$query = $modalUrl->getQueryParameters();
			unset($query["do"]);
			$modalUrl->setQuery($query);
		}

		foreach ($modalUrl->getQueryParameters() as $key => $value) {
			$prefix = ModalExtension::CONTROL_NAME . "-";
			$len = strlen($prefix);
			if (substr($key, 0, $len) === $prefix) {
				$modalUrl->setQueryParameter($key, NULL);
			}
		}

		$modalUrl->setQueryParameter("do", ModalExtension::CONTROL_NAME . "-" . $this->getId());
		return $modalUrl;
	}


	/**
	 * @return ModalControl
	 */
	public function getInstance()
	{
		if (!$this->instance) {
			$this->instance = $this->createInstance();
		}
		return $this->instance;
	}

	/**
	 * @return ModalControl
	 */
	public function createInstance()
	{
		/** @var ModalControl $modalControl */
		$modalControl = $this->service->create();
		$modalControl->setModalFactory($this);
		return $modalControl;
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getInterface(): string
	{
		return $this->interface;
	}

	/**
	 * @return object
	 */
	public function getService(): object
	{
		return $this->service;
	}

	/**
	 * @param array $parameters
	 * @return Url
	 */
	public function getUrl(array $parameters = []): Url
	{
		if (!$parameters) {
			return $this->url;
		}

		$url = clone $this->url;
		foreach ($parameters as $key => $value) {
			$url->setQueryParameter(ModalExtension::CONTROL_NAME . "-" . $this->getId() . "-" . $key, $value);
		}
		if (method_exists($this->getClassName(), 'onCreateLink')) {
			($this->getClassName())::onCreateUrl($this, $url, $parameters);
		}

		return $url;
	}

	/**
	 * @param array $parameters
	 * @param string $class
	 * @return Html
	 */
	public function createLink(array $parameters = [], string $class = "btn btn-default"): Html
	{
		return Html::el("a", [
			"href" => $this->getUrl($parameters),
			"class" => $class,
		]);
	}

	/**
	 * @return Request
	 */
	public function getRequest(): Request
	{
		return $this->request;
	}

	/**
	 * @return Driver
	 */
	public function getDriver(): Driver
	{
		return $this->driver;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 * @return $this
	 */
	public function setActive($active)
	{
		$this->active = $active;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getClassName(): string
	{
		return $this->className;
	}

}
