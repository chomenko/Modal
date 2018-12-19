<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 16.12.2018
 */

namespace Chomenko\Modal\Events;

use Chomenko\Modal\ModalControl;

class Event
{

	/**
	 * @var callable
	 */
	private $callable;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string|null
	 */
	private $interface;

	/**
	 * @param callable $callable
	 * @param string $type
	 * @param string|NULL $interface
	 */
	public function __construct(callable $callable, string $type, string $interface = null)
	{
		$this->callable = $callable;
		$this->type = $type;
		$this->interface = $interface;
	}

	/**
	 * @param ModalControl $modalControl
	 * @param array $args
	 * @return mixed
	 */
	public function emit(ModalControl $modalControl, array $args = [])
	{
		$parameters = [$modalControl];
		$parameters = array_merge($parameters, $args);
		return call_user_func_array($this->callable, $parameters);
	}

	/**
	 * @return bool
	 */
	public function isGlobal(): bool
	{
		return !$this->interface;
	}

	/**
	 * @return callable
	 */
	public function getCallable(): callable
	{
		return $this->callable;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return null|string
	 */
	public function getInterface(): ?string
	{
		return $this->interface;
	}

}
