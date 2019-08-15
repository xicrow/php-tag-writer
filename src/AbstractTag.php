<?php
declare(strict_types=1);

namespace Xicrow\PhpTagWriter;

/**
 * Class AbstractTag
 *
 * @package Xicrow\PhpTagWriter
 */
abstract class AbstractTag implements RenderableInterface
{
	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * @var mixed[]
	 */
	protected $attributes = [];

	/**
	 * @var bool[]|float[]|int[]|string[]|RenderableInterface[]
	 */
	protected $content = [];

	/**
	 * @var bool
	 */
	protected $removeContentWhitespace = false;

	/**
	 * Static constructor, array argument will set as attributes, scalar|RenderableInterface argument will set as content
	 *
	 * @param string $tagName
	 * @param mixed  ...$args
	 * @return static
	 */
	public static function create(string $tagName = '', ...$args)
	{
		$attributes = [];
		$content    = null;
		if (isset($args[0]) && is_array($args[0])) {
			$attributes = $args[0];
		}
		if (isset($args[0]) && (is_scalar($args[0]) || $args[0] instanceof RenderableInterface)) {
			$content = $args[0];
		}
		if (isset($args[1]) && is_array($args[1])) {
			$attributes = $args[1];
		}
		if (isset($args[1]) && (is_scalar($args[1]) || $args[1] instanceof RenderableInterface)) {
			$content = $args[1];
		}

		return new static($tagName, $attributes, $content);
	}

	/**
	 * Dynamic constructor
	 *
	 * @param string                                         $tagName
	 * @param array                                          $attributes
	 * @param bool|float|int|null|string|RenderableInterface $content
	 */
	public function __construct(string $tagName = '', array $attributes = [], $content = null)
	{
		$this->setName($tagName);

		if (count($attributes) > 0) {
			$this->mergeAttributes($attributes);
		}

		if (is_scalar($content) || $content instanceof RenderableInterface) {
			$this->appendContent($content);
		}
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->render();
	}

	/**
	 * @return array
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}

	/**
	 * @param int|string $attribute
	 * @param mixed      $default
	 * @return mixed
	 */
	public function getAttribute($attribute, $default = false)
	{
		if (!array_key_exists($attribute, $this->attributes)) {
			return $default;
		}

		return $this->attributes[$attribute];
	}

	/**
	 * @return bool[]|float[]|int[]|string[]|RenderableInterface[]
	 */
	public function getContent(): array
	{
		return $this->content;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return bool
	 */
	public function getRemoveContentWhitespace(): bool
	{
		return $this->removeContentWhitespace;
	}

	/**
	 * @param array $attributes
	 * @return static
	 */
	public function setAttributes(array $attributes)
	{
		$this->attributes = $attributes;

		return $this;
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @return static
	 */
	public function setAttribute($attribute, $value)
	{
		$this->attributes[$attribute] = $value;

		return $this;
	}

	/**
	 * @param array $content
	 * @return static
	 */
	public function setContent(array $content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * @param string $name
	 * @return static
	 */
	public function setName(string $name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @param bool $removeContentWhitespace
	 * @return static
	 */
	public function setRemoveContentWhitespace(bool $removeContentWhitespace)
	{
		$this->removeContentWhitespace = $removeContentWhitespace;

		return $this;
	}

	/**
	 * @param int|string $attribute
	 * @param mixed      $value
	 * @return static
	 */
	public function appendAttribute($attribute, $value)
	{
		if (array_key_exists($attribute, $this->getAttributes())) {
			$this->mergeAttributes([$attribute => $value]);
		} else {
			$this->setAttributes(array_merge($this->getAttributes(), [$attribute => $value]));
		}

		return $this;
	}

	/**
	 * @param float|int|null|string|RenderableInterface $content
	 * @return static
	 */
	public function appendContent($content)
	{
		$this->setContent(array_merge($this->getContent(), [$content]));

		return $this;
	}

	/**
	 * @param AbstractTag $tag
	 * @return static
	 */
	public function appendTo(AbstractTag $tag)
	{
		$tag->appendContent($this);

		return $this;
	}

	/**
	 * @param int|string $attribute
	 * @param mixed      $value
	 * @return static
	 */
	public function prependAttribute($attribute, $value)
	{
		if (array_key_exists($attribute, $this->getAttributes())) {
			$this->mergeAttributes([$attribute => $value]);
		} else {
			$this->setAttributes(array_merge([$attribute => $value], $this->getAttributes()));
		}

		return $this;
	}

	/**
	 * @param float|int|null|string|RenderableInterface $content
	 * @return static
	 */
	public function prependContent($content)
	{
		$this->setContent(array_merge([$content], $this->getContent()));

		return $this;
	}

	/**
	 * @param AbstractTag $tag
	 * @return static
	 */
	public function prependTo(AbstractTag $tag)
	{
		$tag->prependContent($this);

		return $this;
	}

	/**
	 * @param array $attributes
	 * @return static
	 */
	public function mergeAttributes(array $attributes)
	{
		if (empty($attributes)) {
			return $this;
		}

		if (count($this->getAttributes()) === 0) {
			$this->setAttributes($attributes);

			return $this;
		}

		foreach ($attributes as $attribute => $value) {
			if (!isset($this->getAttributes()[$attribute])) {
				$this->setAttribute($attribute, $value);
				continue;
			}

			$this->mergeAttribute($attribute, $value);
		}

		return $this;
	}

	/**
	 * @param int|string $attribute
	 * @param mixed      $value
	 * @return void
	 */
	protected function mergeAttribute($attribute, $value): void
	{
		if (is_array($this->getAttribute($attribute)) && is_array($value)) {
			$this->setAttribute($attribute, array_merge($this->getAttribute($attribute), $value));
		} elseif (is_string($this->getAttribute($attribute)) && is_string($value)) {
			$this->setAttribute($attribute, $this->getAttribute($attribute) . ' ' . trim($value));
		} else {
			$this->setAttribute($attribute, $value);
		}
	}

	/**
	 * @param bool $pretty
	 * @param int  $level
	 * @return string
	 */
	public function render(bool $pretty = false, int $level = 0): string
	{
		$contentAndChildLevel = $level;
		if ($this->getName() !== '') {
			$contentAndChildLevel = $level + 1;
		}

		$string = '';
		if ($this->getName() !== '') {
			$string .= $this->renderOpenTag($pretty, $level);
		}

		foreach ($this->getContent() as $content) {
			if (is_scalar($content)) {
				$string .= $this->renderStringContent((string)$content, $pretty, $contentAndChildLevel);
			} elseif ($content instanceof RenderableInterface) {
				$string .= $content->render($pretty, $contentAndChildLevel);
			}
		}
		if ($this->getName() !== '') {
			$string .= $this->renderCloseTag($pretty, $level);
		}

		return $string;
	}

	/**
	 * @param string $content
	 * @param bool   $pretty
	 * @param int    $level
	 * @return string
	 */
	protected function renderStringContent(string $content, bool $pretty = false, int $level = 0): string
	{
		$string = '';
		if ($pretty) {
			$firstLineIndentation = null;
			$content              = ltrim($content, "\n");
			$content              = rtrim($content, "\n\t");
			$lines                = explode("\n", $content);
			foreach ($lines as $line) {
				if (ctype_space($line)) {
					continue;
				}

				if ($firstLineIndentation === null) {
					$firstLineIndentation = substr_count($line, "\t");
				}

				$string .= "\n";
				if ($level >= $firstLineIndentation) {
					$string .= str_repeat("\t", ($level - $firstLineIndentation));
				} else {
					$line = substr($line, ($firstLineIndentation - $level));
				}
				$string .= $line;
			}
		} else {
			if ($this->getRemoveContentWhitespace()) {
				$content = trim($content);
				$content = str_replace(["\n", "\r", "\t"], '', $content);
			}
			$string .= $content;
		}

		return $string;
	}

	/**
	 * @param bool $pretty
	 * @param int  $level
	 * @return string
	 */
	protected function renderOpenTag(bool $pretty = false, int $level = 0): string
	{
		$string = '';
		if ($pretty) {
			$string .= "\n";
			$string .= str_repeat("\t", $level);
		}
		$string .= '<';
		$string .= $this->getName();
		$string .= $this->renderAttributes();
		$string .= '>';

		return $string;
	}

	/**
	 * @param bool $pretty
	 * @param int  $level
	 * @return string
	 */
	protected function renderCloseTag(bool $pretty = false, int $level = 0): string
	{
		$string = '';
		if ($pretty) {
			if (count($this->getContent()) > 0) {
				$string .= "\n";
				$string .= str_repeat("\t", $level);
			}
		}
		$string .= '</';
		$string .= $this->getName();
		$string .= '>';

		return $string;
	}

	/**
	 * @return string
	 */
	protected function renderAttributes(): string
	{
		$array = [];
		foreach ($this->getAttributes() as $attribute => $value) {
			$array[] = $this->renderAttribute($attribute, $value);
		}
		$array = array_filter($array);

		$string = '';
		if (count($array)) {
			$string = ' ' . implode(' ', $array);
		}

		return $string;
	}

	/**
	 * @param int|string $attribute
	 * @param mixed      $value
	 * @return string
	 */
	protected function renderAttribute($attribute, $value): string
	{
		if (is_int($attribute) && is_scalar($value)) {
			return trim((string)$value);
		}

		$attribute = trim($attribute);

		if (is_array($value)) {
			$value = implode(' ', $value);
		}
		if (is_bool($value)) {
			$value = (int)$value;
		}
		if (is_object($value) && method_exists($value, '__toString')) {
			$value = (string)$value;
		}
		if (is_object($value)) {
			$value = json_encode($value);
		}

		$value = trim((string)$value);
		$value = str_replace('"', '\'', $value);

		return $attribute . '="' . $value . '"';
	}
}
