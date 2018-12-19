<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 14.12.2018
 */

namespace Chomenko\Modal;

interface IModalControl
{

	/**
	 * @param callable $callable
	 * @return mixed
	 */
	public function onAttached(callable $callable);

	/**
	 * @param AccessAction $accessAction
	 * @return bool
	 */
	public function access(AccessAction $accessAction): bool;

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @return mixed
	 */
	public function renderHeader(WrappedHtml $wrappedHtml);

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @return mixed
	 */
	public function renderBody(WrappedHtml $wrappedHtml);

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @return mixed
	 */
	public function renderFooter(WrappedHtml $wrappedHtml);

}
