<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Chomenko\Modal\Events;

use Core\BundlePresenters\CommonPresenter;
use Kdyby\Events\Subscriber;
use Nette\Application\Application;
use Nette\Application\UI\Presenter;

class ModalDriverListener implements Subscriber
{

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
		if ($presenter instanceof CommonPresenter) {
			$presenter->onStartup[] = function () use ($presenter) {
				foreach ($presenter->modalController->getModels() as $model) {
					$model->getDriver()->attach($presenter);
				}
			};
		}
	}

}
