<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 16.12.2018
 */

namespace Chomenko\Modal\Events;

use Chomenko\Modal\Exceptions\ModalException;
use Chomenko\Modal\ModalControl;
use Chomenko\Modal\ModalFactory;

class EventListener
{

	/**
	 * @var Event[]
	 */
	private $list = [];

	/**
	 * @param Event $event
	 */
	public function install(Event $event)
	{
		$this->list[] = $event;
	}

	/**
	 * @param IEvents $events
	 * @throws ModalException
	 */
	public function registered(IEvents $events)
	{
		$eventsList = $events->getSubscribedEvents();
		if (empty($eventsList)) {
			throw ModalException::mustReturnEventsList(get_class($events));
		}
		foreach ($eventsList as $type) {
			$callable = [$events, $type];
			$event = new Event($callable, $type);
			$this->install($event);
		}
	}


	/**
	 * @param string $type
	 * @param ModalControl $control
	 * @param ModalFactory $factory
	 * @param array $args
	 * @return mixed|null
	 */
	public function emit(string $type, ModalControl $control, ModalFactory $factory, array $args = [])
	{
		$result = NULL;
		foreach ($this->getByType($type) as $event) {
			if ($event->isGlobal() || $factory->getInterface() === $event->getInterface()) {
				$result = $event->emit($control, $args);
			}
		}
		return $result;
	}

	/**
	 * @param string $type
	 * @return Event[]
	 */
	public function getByType(string $type)
	{
		$list = [];

		foreach ($this->list as $event) {
			if ($event->getType() === $type) {
				$list[] = $event;
			}
		}
		return $list;
	}

}
