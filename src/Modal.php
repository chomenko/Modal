<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 14.12.2018
 */

namespace Chomenko\Modal;

trait Modal
{

	/**
	 * @var IWrappedModal @inject
	 */
	public $modalFactory;

	/**
	 * @return WrappedModal
	 */
	public function createComponentModal()
	{
		/** @var WrappedModal $wrapped */
		$wrapped = $this->modalFactory->create();
		return $wrapped;
	}

}
