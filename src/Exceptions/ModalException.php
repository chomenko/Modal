<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 11.11.2018 12:35
 */

namespace Chomenko\Modal\Exceptions;

class ModalException extends \Exception
{

	/**
	 * @return ModalException
	 */
	public static function accessDenied()
	{
		return new self("Modal access denied");
	}

	/**
	 * @param string $class
	 * @return ModalException
	 */
	public static function mustReturnEventsList(string $class)
	{
		return new self("Modal events class {$class} must return events list");
	}

	/**
	 * @param string $name
	 * @return ModalException
	 */
	public static function modalNotFound(string $name)
	{
		return new self("Modal '{$name}' not found");
	}

	/**
	 * @param string $componentName
	 * @param string $parameter
	 * @return ModalException
	 */
	public static function modalRequiredParameter(string $componentName, string $parameter)
	{
		return new self("Component '{$componentName}' is required parameter '{$parameter}'");
	}

	/**
	 * @param string $interface
	 * @return ModalException
	 */
	public static function interfaceNotFound(string $interface)
	{
		return new self("Interface '{$interface}' not found. The modal must be an interface factory. Did you add it to the configuration file?");
	}

}
