<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

use NicMart\Generics\Example\Product\Tuple2«FullName·RelativeName»;

$full = FullName::fromString("full");
$relative = RelativeName::fromString("relative");

$tuple = new Tuple2«FullName·RelativeName»(
    $full,
    $relative
);

$this->assertEquals(
    $full,
    $tuple->_1()
);

$this->assertEquals(
    $relative,
    $tuple->_2()
);

$elements = array();

foreach ($tuple->iterator() as $element) {
    $elements[] = $element;
}

$this->assertEquals(
    array($full, $relative),
    $elements
);