<?php
declare(strict_types=1);

namespace Xicrow\PhpTagWriter;

/**
 * Interface Renderable
 *
 * @package Xicrow\PhpHtml
 */
interface RenderableInterface
{
	/**
	 * Render the current object as a string
	 *
	 * @param bool $pretty
	 * @param int  $level
	 * @return string
	 */
	public function render(bool $pretty = false, int $level = 0): string;
}
