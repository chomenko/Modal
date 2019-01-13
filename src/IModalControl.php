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
	 * @param ModalHtml $header
	 */
	public function renderHeader(WrappedHtml $wrappedHtml, ModalHtml $header);

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @param ModalHtml $body
	 */
	public function renderBody(WrappedHtml $wrappedHtml, ModalHtml $body);

	/**
	 * @param WrappedHtml $wrappedHtml
	 * @param ModalHtml $footer
	 */
	public function renderFooter(WrappedHtml $wrappedHtml, ModalHtml $footer);

}
