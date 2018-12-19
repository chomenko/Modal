<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 11.11.2018 12:13
 */

namespace Chomenko\Modal\DI;

use Chomenko\Modal\Events\EventListener;
use Chomenko\Modal\IWrappedModal;
use Chomenko\Modal\ModalController;
use Chomenko\Modal\Tracy\Panel;
use Chomenko\Modal\WrappedModal;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;


class ModalExtension extends CompilerExtension
{

	const TAG_EVENT = 'modal.events';
	const TAG_FACTORY = 'modal.factory';
	const KEY_PROVIDER = '@modal.provider';
	const CONTROL_NAME = 'modal';

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix("modal"))
            ->setFactory(ModalController::class);

		$builder->addDefinition($this->prefix("event"))
			->setFactory(EventListener::class);

		$builder->addDefinition($this->prefix("wrapped"))
			->setFactory(WrappedModal::class)
			->setImplement(IWrappedModal::class);

		$builder->addDefinition($this->prefix('panel'))
			->setFactory(Panel::class);

		$builder->getDefinition('tracy.bar')
			->addSetup('$service->addPanel($this->getService(?));',array($this->prefix('panel')));
    }


    public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$service = $builder->getDefinition($this->prefix("modal"));

		foreach ($builder->findByTag(self::TAG_FACTORY) as $name => $item) {
			$definition = $builder->getDefinition($name);
			$type = $definition->getImplement() ? $definition->getImplement() : $definition->getType();
			$service->addSetup("addModal", [$type]);
		}

		$event = $builder->getDefinition($this->prefix("event"));
		foreach ($builder->findByTag(self::TAG_EVENT) as $name => $item) {
			$event->addSetup("registered", [$builder->getDefinition($name)]);
		}

		$engine = $builder->getDefinition('nette.latteFactory');
		$engine->addSetup('Chomenko\Modal\Macros\Latte::install(?, ?)', [
			"@self", $builder->getDefinition($this->prefix("modal"))
		]);
	}

	/**
     * @param Configurator $configurator
     */
    public static function register(Configurator $configurator)
    {
        $configurator->onCompile[] = function ($config, Compiler $compiler){
            $compiler->addExtension('AppWebLoader', new AppWebLoaderExtension());
        };
    }

}