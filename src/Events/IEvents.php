<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 16.12.2018
 */

namespace Chomenko\Modal\Events;

use Chomenko\Modal\AccessAction;
use Chomenko\Modal\ModalControl;
use Chomenko\Modal\WrappedHtml;

interface IEvents
{

	/**
	 * @return array
	 */
	public function getSubscribedEvents(): array;

	/**
	 * @param ModalControl $modalControl
	 */
	public function create(ModalControl $modalControl);

	/**
	 * @param ModalControl $modalControl
	 * @param AccessAction $accessAction
	 * @return bool allow access
	 */
	public function access(ModalControl $modalControl, AccessAction $accessAction): bool;

	/**
	 * @param ModalControl $modalControl
	 * @param AccessAction $accessAction
	 * @return bool Throw enable
	 */
	public function accessFailure(ModalControl $modalControl, AccessAction $accessAction): bool;

	/**
	 * @param ModalControl $modalControl
	 * @param AccessAction $accessAction
	 * @return bool Throw enable
	 */
	public function accessSuccess(ModalControl $modalControl, AccessAction $accessAction);

	/**
	 * @param ModalControl $modalControl
	 * @param WrappedHtml $wrapped
	 * @return mixed
	 */
	public function afterRender(ModalControl $modalControl, WrappedHtml $wrapped);

	/**
	 * @param ModalControl $modalControl
	 * @param WrappedHtml $wrapped
	 * @return mixed
	 */
	public function beforeRender(ModalControl $modalControl, WrappedHtml $wrapped);

}
