<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 16.12.2018
 */

namespace Chomenko\Modal;

use Chomenko\Modal\DI\ModalExtension;
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
	 * @var Url
	 */
	private $url;

	public function __construct(string $interface, object $service)
	{
		$this->id = hash("crc32b", $interface);
		$this->interface = $interface;
		$this->service = $service;
		$this->url = $this->createUrl();
	}

	/**
	 * @return Url
	 */
	private function createUrl()
	{
		$actualLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$modalUrl = new Url($actualLink);

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
		return $this->service->create();
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

}
