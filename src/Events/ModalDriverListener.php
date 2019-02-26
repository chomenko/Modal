<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Chomenko\Modal\Events;

use Chomenko\Modal\ModalController;
use Kdyby\Events\Subscriber;
use Nette\Application\Application;
use Nette\Application\UI\Presenter;

class ModalDriverListener implements Subscriber
{

	/**
	 * @var ModalController
	 */
	public $modalController;

	public function __construct(ModalController $modalController)
	{
		$this->modalController = $modalController;
	}

	public function getSubscribedEvents(): array
	{
		if (php_sapi_name() == "cli") {
			return [];
		}
		return [
			Application::class . "::onPresenter" => "onPresenter",
		];
	}

	/**
	 * @param Application $application
	 * @param Presenter $presenter
	 */
	public function onPresenter(Application $application, Presenter $presenter)
	{
		$presenter->onStartup[] = function () use ($presenter) {
			foreach ($this->modalController->getModels() as $model) {
				$model->getDriver()->attach($presenter);
			}
		};
	}

}
