<?php
declare(strict_types=1);

namespace Xicrow\PhpTagWriter;

/**
 * Class HtmlTag
 *
 * @package Xicrow\PhpTagWriter
 */
class HtmlTag extends AbstractTag
{
	/**
	 * @inheritDoc
	 */
	protected function mergeAttribute($attribute, $value): void
	{
		if ($attribute === 'class') {
			if (is_array($this->attributes[$attribute]) && is_string($value)) {
				$value = explode(' ', $value);
			}
			if (is_string($this->attributes[$attribute]) && is_array($value)) {
				$value = implode(' ', $value);
			}
		}

		parent::mergeAttribute($attribute, $value);
	}

	/**
	 * @inheritDoc
	 */
	protected function renderOpenTag(bool $pretty = false, int $level = 0): string
	{
		$string = '';
		if ($pretty) {
			$string .= "\n";
			$string .= str_repeat("\t", $level);
		}
		$string .= '<';
		$string .= $this->name;
		$string .= $this->renderAttributes();
		if ($this->isVoidElement() && $this->name !== '!doctype') {
			$string .= '/>';
		} else {
			$string .= '>';
		}

		return $string;
	}

	/**
	 * @inheritDoc
	 */
	protected function renderCloseTag(bool $pretty = false, int $level = 0): string
	{
		$string = '';
		if (!$this->isVoidElement() && $this->name !== '!doctype') {
			if ($pretty) {
				if (count($this->content) > 0) {
					$string .= "\n";
					$string .= str_repeat("\t", $level);
				}
			}
			$string .= '</';
			$string .= $this->name;
			$string .= '>';
		}

		return $string;
	}

	/**
	 * @inheritDoc
	 */
	protected function renderAttribute($attribute, $value): string
	{
		if ($attribute === 'data' && is_array($value)) {
			return $this->renderAttributeData($value);
		}
		if ($attribute === 'style' && is_array($value)) {
			return $this->renderAttributeStyle($value);
		}

		if ($attribute === 'checked' && is_bool($value)) {
			$value = ($value ? 'checked' : '');
		}
		if ($attribute === 'selected' && is_bool($value)) {
			$value = ($value ? 'selected' : '');
		}

		return parent::renderAttribute($attribute, $value);
	}

	/**
	 * @param array $data
	 * @return string
	 */
	protected function renderAttributeData(array $data): string
	{
		$array = [];
		foreach ($data as $key => $value) {
			$array[] = 'data-' . trim($key) . '="' . str_replace('"', '\'', trim($value)) . '"';
		}

		return ' ' . implode(' ', $array);
	}

	/**
	 * @param array $styles
	 * @return string
	 */
	protected function renderAttributeStyle(array $styles): string
	{
		$array = [];
		foreach ($styles as $property => $value) {
			$array[] = trim($property) . ': ' . trim($value, ' ;') . ';';
		}

		$string = '';
		if (count($array)) {
			$string = implode(' ', $array);
		}

		return ' style="' . $string . '"';
	}

	/**
	 * @return bool
	 */
	protected function isVoidElement(): bool
	{
		return in_array($this->name, [
			'area',
			'base',
			'br',
			'col',
			'command',
			'embed',
			'hr',
			'img',
			'input',
			'keygen',
			'link',
			'menuitem',
			'meta',
			'param',
			'source',
			'track',
			'wbr',
		]);
	}
}
