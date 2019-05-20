# Bootstrap Modal
Required
 - php 7.1
 - [Bootstrap](https://getbootstrap.com/docs/3.3/)
 - [JQuery](https://github.com/jquery/jquery)
 - [chomenko/app-webloader](https://github.com/chomenko/AppWebLoader)
 - [nette/application](https://github.com/nette/application)
 - [nette/utils](https://github.com/nette/utils)
 - [tracy/tracy](https://github.com/nette/tracy)
 - [latte/latte](https://github.com/nette/latte)

## Install

````sh
composer require chomenko/modal
````

## Configuration

Add extension
````neon
extensions:
    modals: Chomenko\Modal\DI\ModalExtension
````

Add to BasePresenter.php
````php
namespace App;

use Chomenko\Modal\IWrappedModal;

abstract class BasePresenter extends Presenter
	/**
	 * @var IWrappedModal @inject
	 */
	public $modalFactory;

	public function createComponentModal()
	{
		/** @var WrappedModal $wrapped */
		$wrapped = $this->modalFactory->create();
		return $wrapped;
	}
}
````

Add to layout.late
````latte
<!DOCTYPE html>
<html>
<head>
	{control css}
</head>

<body class="skin-purple sidebar-mini fixed">
	{control modal}
	{control footerCss}
	{block scripts}
    		{control js}
    	{/block}
</body>
</html>
````

## Use

Create your first modal. Create file HelloWorldModal.php podivej se na ``Chomenko\Modal\ModalControl``

````php
<?php

namespace App\Components;

use Chomenko\AutoInstall\AutoInstall;
use Chomenko\AutoInstall\Config;
use Chomenko\Modal\ModalHtml;
use Chomenko\Modal\WrappedHtml;
use Chomenko\Modal\ModalControl;
use Chomenko\Modal\ModalFactory;
use Chomenko\Modal\AccessAction;
use Nette\Http\Url;

/**
 * @Config\Tag({"modal.factory"})
 */
class HelloWorldModal extends ModalControl implements AutoInstall
{
	
	/**
	 * @var $id
	 */
	private $id;

	/**
	 * @param int $id
	 */
	public function create($id)
	{
		$this->id = $id;
	}
	
	/**
	 * @param ModalFactory $factory
	 * @param Url $url
	 * @param array $parameters
	 * @throws \Exception
	 */
	public static function onCreateUrl(ModalFactory $factory, Url $url, array $parameters = [])
	{
	}
	
	
	/**
	 * @param AccessAction $accessAction
	 * @return bool
	 */
	public function access(AccessAction $accessAction): bool
	{
		return $accessAction->getUser()->isLoggedIn();
	}

	/**
	 * @return string
	 */
	public function getTitle() :string
	{
		return "Hello World";
	}

	/**
	 * @param WrappedHtml $wrappedHtml
	 */
	public function renderBody(WrappedHtml $wrappedHtml, ModalHtml $body)
	{
		$this->template->id = $this->id;
		$this->template->setFile(__DIR__ . "/body.latte");
		$this->template->render();
	}

}
````

Create file IHelloWorldModal.php

````php
namespace App\Components;

interface IHelloWorldModal
{
	/**
	 * @return HelloWorldModal
	 */
	public function create();
}
````

Create file body.latte

````latte
<div class="modal-body">
	<h1>Hello World</h1>
</div>
````

And create modal link
````latte
<a href="{mlink 'bf1318e1', id => 123}">Hello world modal</a>

{* OR *}

<a href="{mlink 'App\Components\IHelloWorldModal', id => 123}">Hello world modal</a>

{* OR *}

<a n:mlink="'bf1318e1', id => 123">Hello world modal</a>

{* OR *}

<a n:mlink="'App\Components\IHelloWorldModal', id => 123">Hello world modal</a>
````

Link can also be created manually.
````php

/** @var \Chomenko\Modal\ModalController $modalController */
$modalController = $this->container->getByType(ModalController::class);

/** @var \Chomenko\Modal\ModalFactory $factory */
$factory = $modalController->getByInterface(IHelloWorldModal::class);

/** @var \Nette\Http\Url $url */
$url = $factory->getUrl(["my" => "parameter"]);

````

If you do not use [auto-install](https://github.com/chomenko/AutoInstall).

````neon
services:
    helloWorldModal:
    	implement: App\Components\IHelloWorldModal
    	tags: ["modal.factory"]
````

## Events
Event list
- Subscriber::CREATE
- Subscriber::ACCESS
- Subscriber::ACCESS_FAILURE
- Subscriber::ACCESS_SUCCESS
- Subscriber::AFTER_RENDER
- Subscriber::BEFORE_RENDER

````php
namespace App\Components;

use Chomenko\AutoInstall\AutoInstall;
use Chomenko\AutoInstall\Tag;
use Chomenko\Modal\AccessAction;
use Chomenko\Modal\Events\Subscriber;
use Chomenko\Modal\ModalControl;

/**
 * @Tag({"modal.events"})
 */
class ModalEvent extends Subscriber implements AutoInstall
{

	public function getSubscribedEvents(): array
	{
		return [
			Subscriber::ACCESS
		];
	}

	public function access(ModalControl $modalControl, AccessAction $accessAction): bool
	{
		return TRUE;
	}

}
````

