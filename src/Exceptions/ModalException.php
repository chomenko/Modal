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
	 * @return ModalException
	 */
	public static function mustReturnEventsList(string $class)
	{
		return new self("Modal events class {$class} must return events list");
	}

	/**
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
}
