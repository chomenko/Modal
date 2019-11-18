<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Chomenko\Modal\Events;

use Chomenko\Modal\Exceptions\ModalException;
use Chomenko\Modal\ModalController;
use Kdyby\Events\Subscriber;
use Nette\Application\Application;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\Responses\TextResponse;
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
			Application::class . "::onResponse" => "onResponse",
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

	/**
	 * @param Application $application
	 * @param JsonResponse|TextResponse $response
	 * @throws ModalException
	 */
	public function onResponse(Application $application, $response)
	{
		foreach ($this->modalController->getClosedModals() as $modal) {
			if (!$response instanceof JsonResponse) {
				return;
			}
			$payload = $response->getPayload();
			if (!$payload instanceof \stdClass) {
				return;
			}
			if (!is_array($payload->modal)) {
				return;
			}
			if (isset($payload->modal[$modal->getId()])) {
				return;
			}
			$payload->modal[$modal->getId()] = $modal->getDriver()->getPayload();
		}
	}

}
