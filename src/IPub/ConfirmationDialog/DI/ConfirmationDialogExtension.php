<?php
/**
 * ConfirmationDialogExtension.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:ConfirmationDialog!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           08.06.14
 */

declare(strict_types = 1);

namespace IPub\ConfirmationDialog\DI;

use Nette;
use Nette\DI;
use Nette\PhpGenerator as Code;

use IPub\ConfirmationDialog\Components;
use IPub\ConfirmationDialog\Storage;
use Nette\Schema\Expect;

/**
 * Confirmation dialog extension container
 *
 * @package        iPublikuj:ConfirmationDialog!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class ConfirmationDialogExtension extends DI\CompilerExtension
{

	public function getConfigSchema(): Nette\Schema\Schema
	{
		return Expect::structure([
			'layoutFile' => Expect::string()->nullable(),
			'templateFile' => Expect::string()->nullable(),
		]);
	}

	/**
	 * @return void
	 */
	public function loadConfiguration() : void
	{
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		// Session storage
		$builder->addDefinition($this->prefix('storage'))
			->setType(Storage\Session::class);

		$confirmerFactory = $builder->addFactoryDefinition($this->prefix('confirmer'))
			->setImplement(Components\IConfirmer::class)
			->addTag('nette.inject');
		$confirmerFactory->getResultDefinition()
			->setType(Components\Confirmer::class)
			->setArguments([new Code\PhpLiteral('$templateFile')])
			->setAutowired(FALSE);

		// Define components factories
		$dialogFactory = $builder->addFactoryDefinition($this->prefix('dialog'))
			->setImplement(Components\IControl::class)
			->addTag('nette.inject');
		$dialogFactory->getResultDefinition()
			->setType(Components\Control::class)
			->setArguments([
				new Code\PhpLiteral('$layoutFile'),
				new Code\PhpLiteral('$templateFile'),
				$confirmerFactory,
			]);

		if ($config->layoutFile) {
			$dialogFactory->getResultDefinition()->addSetup('$service->setLayoutFile(?)', [$config->layoutFile]);
		}

		if ($config->templateFile) {
			$dialogFactory->getResultDefinition()->addSetup('$service->setTemplateFile(?)', [$config->templateFile]);
		}
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'confirmationDialog') : void
	{
		$config->onCompile[] = function (Nette\Configurator $config, DI\Compiler $compiler) use ($extensionName) : void {
			$compiler->addExtension($extensionName, new ConfirmationDialogExtension());
		};
	}

	/**
	 * Return array of directories, that contain resources for translator.
	 *
	 * @return string[]
	 */
	public function getTranslationResources() : array
	{
		return [
			__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Translations'
		];
	}
}
