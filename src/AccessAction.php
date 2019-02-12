<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 16.12.2018
 */

namespace Chomenko\Modal;

use Nette\Application\Helpers;
use Nette\Application\UI\Presenter;
use Nette\Security\User;

class AccessAction
{

	/**
	 * @var Presenter
	 */
	private $presenter;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var string
	 */
	private $module;

	/**
	 * @var string
	 */
	private $presenterName;

	/**
	 * @var bool
	 */
	private $allowed = FALSE;

	public function __construct(Presenter $presenter, User $user)
	{
		$this->presenter = $presenter;
		[$model, $presenterName] = Helpers::splitName($this->presenter->getName());
		$this->module = $model;
		$this->presenterName = $presenterName;
		$this->user = $user;
	}


	/**
	 * @return Presenter
	 */
	public function getPresenter(): Presenter
	{
		return $this->presenter;
	}

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getModule(): string
	{
		return $this->module;
	}

	/**
	 * @return string
	 */
	public function getPresenterName(): string
	{
		return $this->presenterName;
	}

	/**
	 * @return bool
	 */
	public function isAllowed(): bool
	{
		return $this->allowed;
	}

	/**
	 * @param bool $allowed
	 * @return $this
	 */
	public function setAllowed($allowed)
	{
		$this->allowed = $allowed;
		return $this;
	}

}
