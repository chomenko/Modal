<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 14.12.2018
 */

namespace Chomenko\Modal;

use Chomenko\Modal\Exceptions\ModalException;
use Nette\DI\Container;
use Nette\Http\Request;

class ModalController
{

	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @var IWrappedModal
	 */
	private $modalFactory;

	/**
	 * @var ModalFactory[]
	 */
	private $modal = [];

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @param Container $container
	 * @param IWrappedModal $modalFactory
	 * @param Request $request
	 */
	public function __construct(Container $container, IWrappedModal $modalFactory, Request $request)
	{
		$this->container = $container;
		$this->modalFactory = $modalFactory;
		$this->request = $request;
	}

	/**
	 * @param string $interface
	 * @throws ModalException
	 */
	public function addModal(string $interface)
	{
		if (!interface_exists($interface)) {
			throw ModalException::interfaceNotFound($interface);
		}
		$factory = new ModalFactory($interface, $this->container->getByType($interface), $this->request);
		$this->modal[$factory->getId()] = $factory;
	}

	/**
	 * @param string $id
	 * @return ModalFactory
	 */
	public function getById(string $id): ?ModalFactory
	{
		if (array_key_exists($id, $this->modal)) {
			return $this->modal[$id];
		}
		return NULL;
	}

	/**
	 * @param string $interface
	 * @return ModalFactory|null
	 */
	public function getByInterface(string $interface): ?ModalFactory
	{
		foreach ($this->modal as $modal) {
			if ($modal->getInterface() === $interface) {
				return $modal;
			}
		}
		return NULL;
	}

	/**
	 * @return ModalFactory[]
	 */
	public function getModels(): array
	{
		return $this->modal;
	}

}
