<?php
declare(strict_types=1);

namespace Xicrow\PhpTagWriter;

/**
 * Class XmlTag
 *
 * @package Xicrow\PhpTagWriter
 */
class XmlTag extends AbstractTag
{
	/**
	 * @var bool
	 */
	protected $wrapContentInCdata = false;

	/**
	 * @return bool
	 */
	public function getWrapContentInCdata(): bool
	{
		return $this->wrapContentInCdata;
	}

	/**
	 * @param bool $wrapContentInCdata
	 * @return static
	 */
	public function setWrapContentInCdata(bool $wrapContentInCdata)
	{
		$this->wrapContentInCdata = $wrapContentInCdata;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	protected function renderOpenTag(bool $pretty = false, int $level = 0): string
	{
		if (substr($this->name, 0, 1) === '?' || substr($this->name, 0, 1) === '!') {
			$string = '';
			if ($pretty) {
				$string .= "\n";
				$string .= str_repeat("\t", $level);
			}
			$string .= '<';
			$string .= $this->name;
			$string .= $this->renderAttributes();
			if(substr($this->name, 0, 1) === '?') {
				$string .= '?>';
			} else {
				$string .= '>';
			}

			return $string;
		}

		return parent::renderOpenTag($pretty, $level);
	}

	/**
	 * @inheritDoc
	 */
	protected function renderCloseTag(bool $pretty = false, int $level = 0): string
	{
		if (substr($this->name, 0, 1) === '?' || substr($this->name, 0, 1) === '!') {
			return '';
		}

		return parent::renderCloseTag($pretty, $level);
	}

	/**
	 * @inheritDoc
	 */
	protected function renderStringContent(string $content, bool $pretty = false, int $level = 0): string
	{
		if ($this->getWrapContentInCdata()) {
			$content = '<![CDATA[' . $content . ']]>';
		}

		return parent::renderStringContent($content, $pretty, $level);
	}
}
