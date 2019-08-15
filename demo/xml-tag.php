<?php
require_once('../vendor/autoload.php');

function tag(string $tagName = '', ...$args)
{
	return Xicrow\PhpTagWriter\XmlTag::create($tagName, ...$args);
}

// ------------------------------------------------------------------------------------------------

$document = tag();
tag('?xml', [
	'version'  => '1.0',
	'encoding' => 'UTF-8',
])->appendTo($document);
/*
@todo Add support for DTD (Document Type Definition), both inline and external
@see https://www.w3schools.com/xml/xml_dtd.asp
@see https://www.w3schools.com/xml/xml_dtd_intro.asp

Inline example:
	<!DOCTYPE note [
	<!ENTITY nbsp "&#xA0;">
	<!ENTITY writer "Writer: Donald Duck.">
	<!ENTITY copyright "Copyright: W3Schools.">
	]>

External example:
	<!DOCTYPE note SYSTEM "Note.dtd">
*/
tag('!DOCTYPE', [
	'note',
	'SYSTEM',
	'"Note.dtd"',
])->appendTo($document);

$tagBookstore = tag('bookstore')->appendTo($document);

$books = [
	[
		'category' => 'children',
		'title'    => 'Harry Potter',
		'language' => 'en',
		'author'   => 'J K. Rowling',
		'year'     => 2005,
		'price'    => 29.99,
		'currency' => 'GBP',
	],
	[
		'category' => 'cooking',
		'title'    => 'Everyday Italian',
		'language' => 'en',
		'author'   => 'Giada De Laurentiis',
		'year'     => 2005,
		'price'    => 30.00,
		'currency' => 'GBP',
	],
];
foreach ($books as $book) {
	$tagBook = tag('book', [
		'category' => $book['category'],
	])->appendTo($tagBookstore);

	tag('title', $book['title'], [
		'lang' => $book['language'],
	])->appendTo($tagBook)->setWrapContentInCdata(true);

	tag('author', $book['author'])->appendTo($tagBook);

	tag('year', $book['year'])->appendTo($tagBook);

	tag('price', number_format($book['price'], 2), [
		'currency' => $book['currency'],
	])->appendTo($tagBook);

	tag('true', true)->appendTo($tagBook);
	tag('false', false)->appendTo($tagBook);
}

echo $document;
echo '<pre>' . htmlentities($document->render(false)) . '</pre>';
echo '<pre>' . htmlentities($document->render(true)) . '</pre>';
