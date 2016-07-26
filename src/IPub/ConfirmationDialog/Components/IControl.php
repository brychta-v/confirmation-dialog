<?php
/**
 * IControl.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:ConfirmationDialog!
 * @subpackage     Components
 * @since          1.0.0
 *
 * @date           12.03.14
 */

declare(strict_types = 1);

namespace IPub\ConfirmationDialog\Components;

interface IControl
{
	/**
	 * Define class name
	 */
	const INTERFACE_NAME = __CLASS__;

	/**
	 * @param string|NULL $layoutFile
	 * @param string|NULL $templateFile
	 *
	 * @return Control
	 */
	public function create(string $layoutFile = NULL, string $templateFile = NULL) : Control;
}
