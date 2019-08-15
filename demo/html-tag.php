<?php
require_once('../vendor/autoload.php');

function tag(string $tagName = '', ...$args)
{
	return Xicrow\PhpTagWriter\HtmlTag::create($tagName, ...$args);
}

function formInput(string $type, string $name, array $options = [])
{
	$options = array_merge([
		'label'              => null,
		'placeholder'        => null,
		'help'               => null,
		'wrapper_attributes' => [],
		'label_attributes'   => [],
		'input_attributes'   => [],
		'help_attributes'    => [],
	], $options);

	$wrapper = tag('div', [
		'class' => 'field',
		'style' => [
			'position' => 'relative',
		],
	]);
	$wrapper->mergeAttributes($options['wrapper_attributes']);

	if (!empty($options['label'])) {
		$label = tag('label', [
			'for' => 'input-' . strtolower($name),
		]);
		$label->mergeAttributes($options['label_attributes']);
		$label->appendContent($options['label']);
		$label->appendTo($wrapper);
	}

	$input = tag('input', [
		'type'        => $type,
		'name'        => $name,
		'id'          => 'input-' . strtolower($name),
		'placeholder' => $options['placeholder'],
		'onfocus'     => '$(this).siblings(\'.label\').fadeIn()',
		'onblur'      => '$(this).siblings(\'.label\').fadeOut()',
	]);
	$input->mergeAttributes($options['input_attributes']);
	$input->appendTo($wrapper);

	if (!empty($options['help'])) {
		$help = tag('div', [
			'class' => 'ui pointing above label',
			'style' => [
				'display'  => 'none',
				'position' => 'absolute',
				'bottom'   => '-32px',
				'right'    => '0',
				'z-index'  => '100',
			],
		]);
		$help->mergeAttributes($options['help_attributes']);
		$help->appendContent($options['help']);
		$help->appendTo($wrapper);
	}

	return $wrapper;
}

// ------------------------------------------------------------------------------------------------

$page = tag();

tag('!doctype', ['html'])->appendTo($page);

$html = tag('html', ['lang' => 'en'])->appendTo($page);

$head = tag('head')->appendTo($html);
if ('HEAD') {
	tag('meta', [
		'charset' => 'utf-8',
	])->appendTo($head);
	tag('meta', [
		'http-equiv' => 'X-UA-Compatible',
		//	'content'    => 'IE=edge,chrome=1',
		'content'    => 'IE=edge',
	])->appendTo($head);
	tag('meta', [
		'name'    => 'viewport',
		//	'content' => 'width=device-width, initial-scale=1.0, maximum-scale=1.0',
		'content' => 'width=device-width',
	])->appendTo($head);
	tag('title', 'Test')->appendTo($head);
	tag('link', [
		'rel'  => 'stylesheet',
		'type' => 'text/css',
		'href' => 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css',
	])->appendTo($head);
	tag('style', '
		body > .ui.container {
			margin-top: 3em;
		}
		
		.ui.container > h1 {
			font-size: 3em;
			text-align: center;
			font-weight: normal;
		}
		
		.ui.container > h2.dividing.header {
			font-size: 2em;
			font-weight: normal;
			margin: 4em 0em 3em;
		}
		
		.ui.table {
			table-layout: fixed;
		}
		
		.color.grid {
			margin: -1.5em;
			width: 400px;
		}
		.ui.table {
			table-layout: fixed;
		}
		.color.grid .column {
			margin: 0.5em;
			width: 50px;
			height: 50px;
		}
	')->setRemoveContentWhitespace(true)->appendTo($head);
	tag('script', [
		'src'         => 'https://code.jquery.com/jquery-3.1.1.min.js',
		'integrity'   => 'sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=',
		'crossorigin' => 'anonymous',
		'defer',
	])->appendTo($head);
	tag('script', [
		'src' => 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js',
		'defer',
	])->appendTo($head);
	tag('script', '
		$(\'select.dropdown\')
			.dropdown()
		;
	')->setRemoveContentWhitespace(true)->appendTo($head);
}

$body = tag('body')->appendTo($html);

$container = tag('div', [
	'class' => 'ui container',
])->appendTo($body);

tag('h1', 'Theming Examples')->appendTo($container);

if ('FORM' && true) {
	$form = tag('form', [
		'method' => 'get',
		'action' => '',
		'class'  => 'ui form',
	])->appendTo($container);
	formInput('text', 'name', [
		'label'              => 'Name',
		'placeholder'        => 'Enter your name please...',
		'help'               => 'Please enter your full name',
		'wrapper_attributes' => [
			'class' => 'required',
		],
		'input_attributes'   => [
			'class' => 'customer__name',
		],
	])->appendTo($form);
	formInput('text', 'email', [
		'label'              => 'E-mail',
		'placeholder'        => 'Enter your e-mail please...',
		'help'               => 'Please enter your e-mail address',
		'wrapper_attributes' => [
			'class' => 'required',
		],
		'input_attributes'   => [
			'class' => 'customer__email',
		],
	])->appendTo($form);
}

if ('SITE' && false) {
	tag('h2', ['class' => 'ui dividing header'], 'Site')->appendTo($container);

	$grid = tag('div', [
		'class' => 'ui three column stackable grid',
	])->appendTo($container);

	$column = tag('div', [
		'class' => 'column',
	])->appendTo($grid);
	tag('h1', 'Heading 1')->appendTo($column);
	tag('h2', 'Heading 2')->appendTo($column);
	tag('h3', 'Heading 3')->appendTo($column);
	tag('h4', 'Heading 4')->appendTo($column);
	tag('h5', 'Heading 5')->appendTo($column);
	tag('p', [],
		'Nullam quis risus eget urna mollis ornare vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.')->appendTo($column);

	$column = tag('div', [
		'class' => 'column',
	])->appendTo($grid);
	tag('h2', 'Example body text')->appendTo($column);
	tag('p', [],
		'Nullam quis risus eget <a href="#">urna mollis ornare</a> vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.')->appendTo($column);
	tag('p', '<small>This line of text is meant to be treated as fine print.</small>')->appendTo($column);
	tag('p', 'The following snippet of text is <strong>rendered as bold text</strong>.')->appendTo($column);
	tag('p', 'The following snippet of text is <em>rendered as italicized text</em>.')->appendTo($column);
	tag('p', 'An abbreviation of the word attribute is <abbr title="attribute">attr</abbr>.')->appendTo($column);

	$column     = tag('div', [
		'class' => 'column',
	])->appendTo($grid);
	$columnGrid = tag('div', [
		'class' => 'ui three column stackable padded middle aligned centered color grid',
	])->appendTo($column);
	tag('div', ['class' => 'red column'], 'Red')->appendTo($columnGrid);
	tag('div', ['class' => 'orange column'], 'Orange')->appendTo($columnGrid);
	tag('div', ['class' => 'yellow column'], 'Yellow')->appendTo($columnGrid);
	tag('div', ['class' => 'olive column'], 'Olive')->appendTo($columnGrid);
	tag('div', ['class' => 'green column'], 'Green')->appendTo($columnGrid);
	tag('div', ['class' => 'teal column'], 'Teal')->appendTo($columnGrid);
	tag('div', ['class' => 'blue column'], 'Blue')->appendTo($columnGrid);
	tag('div', ['class' => 'violet column'], 'Violet')->appendTo($columnGrid);
	tag('div', ['class' => 'purple column'], 'Purple')->appendTo($columnGrid);
	tag('div', ['class' => 'pink column'], 'Pink')->appendTo($columnGrid);
	tag('div', ['class' => 'brown column'], 'Brown')->appendTo($columnGrid);
	tag('div', ['class' => 'grey column'], 'Grey')->appendTo($columnGrid);
	tag('div', ['class' => 'black column'], 'Black')->appendTo($columnGrid);
}

if ('CARD' && false) {
	tag('h2', ['class' => 'ui dividing header'], 'Card')->appendTo($container);

	$cards = tag('div', [
		'class' => 'ui four cards',
	])->appendTo($container);

	$card = tag('div', [
		'class' => 'card',
	])->appendTo($cards);
	tag('div', ['class' => 'content'], 'Content 1')->appendTo($card);
	tag('div', ['class' => 'content'], 'Content 2')->appendTo($card);
	tag('div', ['class' => 'content'], 'Content 3')->appendTo($card);
	tag('div', ['class' => 'extra content'], 'Extra content')->appendTo($card);
	(clone $card)->appendTo($cards);
	(clone $card)->appendTo($cards);
	(clone $card)->appendTo($cards);

	$card      = tag('div', [
		'class' => 'ui card',
	])->appendTo($cards);
	$cardSlide = tag('div', ['class' => 'ui slide right reveal image'])->appendTo($card);
	tag('div', ['class' => 'visible content'], '<img src="https://semantic-ui.com/examples/assets/images/avatar/nan.jpg" alt="" class="ui fluid image">')->appendTo($cardSlide);
	tag('div', ['class' => 'hidden content'], '<img src="https://semantic-ui.com/examples/assets/images/avatar/tom.jpg" alt="" class="ui fluid image">')->appendTo($cardSlide);
	tag('div', ['class' => 'content'], '<img src="https://semantic-ui.com/examples/assets/images/wireframe/paragraph.png" alt="" class="ui wireframe image">')->appendTo($card);
	(clone $card)->appendTo($cards);

	$card      = tag('div', [
		'class' => 'ui card',
	])->appendTo($cards);
	$cardSlide = tag('div', ['class' => 'ui slide right reveal image'])->appendTo($card);
	tag('div', ['class' => 'visible content'], '<img src="https://semantic-ui.com/examples/assets/images/avatar/tom.jpg" alt="" class="ui fluid image">')->appendTo($cardSlide);
	tag('div', ['class' => 'hidden content'], '<img src="https://semantic-ui.com/examples/assets/images/avatar/nan.jpg" alt="" class="ui fluid image">')->appendTo($cardSlide);
	tag('div', ['class' => 'content'], '<img src="https://semantic-ui.com/examples/assets/images/wireframe/paragraph.png" alt="" class="ui wireframe image">')->appendTo($card);
	(clone $card)->appendTo($cards);
}

echo $page;
echo '<pre>' . htmlentities($page->render(false)) . '</pre>';
echo '<pre>' . htmlentities($page->render(true)) . '</pre>';
