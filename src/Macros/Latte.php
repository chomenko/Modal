<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 18.12.2018
 */

namespace Chomenko\Modal\Macros;

use Chomenko\Modal\DI\ModalExtension;
use Chomenko\Modal\Exceptions\ModalException;
use Chomenko\Modal\ModalController;
use Latte\Compiler;
use Latte\Engine;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Latte\Runtime\Template;

class Latte extends MacroSet
{

	/**
	 * @var ModalController
	 */
	private $modalController;

	/**
	 * @param Compiler $compiler
	 * @param ModalController $modalController
	 */
	public function __construct(Compiler $compiler, ModalController $modalController)
	{
		parent::__construct($compiler);
		$this->modalController = $modalController;
	}

	/**
	 * @param Engine $engine
	 * @param ModalController $modalController
	 * @return static
	 */
	public static function install(Engine $engine, ModalController $modalController)
	{
		$engine->addProvider(ModalExtension::KEY_PROVIDER, $modalController);
		$me = new static($engine->getCompiler(), $modalController);
		$me->addMacro('mlink', [$me, 'macroMLink'], NULL, [$me, 'macroAttrMLink']);
		return $me;
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 * @return string
	 */
	public function macroMLink(MacroNode $node, PhpWriter $writer)
	{
		$class = self::class;
		return $writer->write('echo ' . $class . '::modalUrl($this, %node.array);');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 * @return string
	 */
	public function macroAttrMLink(MacroNode $node, PhpWriter $writer)
	{
		$class = self::class;
		return $writer->write('echo ' . $class . '::modalUrl($this, %node.array, true)');
	}


	public static function modalUrl(Template $template, array $args, bool $href = FALSE)
	{
		/** @var ModalController $controller */
		$controller = NULL;
		foreach ($template->getEngine()->getProviders() as $key => $provider) {
			if ($key == ModalExtension::KEY_PROVIDER) {
				$controller = $provider;
			}
		}

		$id = $args[0];
		unset($args[0]);

		if (!$controller instanceof ModalController) {
			throw ModalException::modalNotFound($id);
		}

		if (!$factory = $controller->getByInterface($id)) {
			$factory = $controller->getById($id);
		}

		if (!$factory) {
			throw ModalException::modalNotFound($id);
		}

		$parameters = [];
		foreach ($args as $key => $value) {
			if (is_string($key)) {
				$parameters[$key] = $value;
				continue;
			}
			$parameters[] = $value;
		}

		if ($href) {
			return ' href="' . $factory->getUrl($parameters) . '" ';
		}
		return $factory->getUrl($parameters);
	}

}
