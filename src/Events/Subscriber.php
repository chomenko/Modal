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

abstract class Subscriber implements IEvents
{

	const CREATE = "create";
	const ACCESS = "access";
	const ACCESS_FAILURE = "accessFailure";
	const ACCESS_SUCCESS = "accessSuccess";
	const AFTER_RENDER = "afterRender";
	const BEFORE_RENDER = "beforeRender";

	/**
	 * @param ModalControl $modalControl
	 */
	public function create(ModalControl $modalControl)
	{
	}

	/**
	 * @param ModalControl $modalControl
	 * @param AccessAction $accessAction
	 * @return bool
	 */
	public function access(ModalControl $modalControl, AccessAction $accessAction): bool
	{
		return TRUE;
	}

	/**
	 * @param ModalControl $modalControl
	 * @param AccessAction $accessAction
	 * @return bool
	 */
	public function accessFailure(ModalControl $modalControl, AccessAction $accessAction): bool
	{
		return TRUE;
	}

	/**
	 * @param ModalControl $modalControl
	 * @param AccessAction $accessAction
	 */
	public function accessSuccess(ModalControl $modalControl, AccessAction $accessAction)
	{
	}

	/**
	 * @param ModalControl $modalControl
	 * @param WrappedHtml $wrapped
	 * @return mixed|void
	 */
	public function afterRender(ModalControl $modalControl, WrappedHtml $wrapped)
	{
	}

	/**
	 * @param ModalControl $modalControl
	 * @param WrappedHtml $wrapped
	 * @return mixed|void
	 */
	public function beforeRender(ModalControl $modalControl, WrappedHtml $wrapped)
	{
	}

}
