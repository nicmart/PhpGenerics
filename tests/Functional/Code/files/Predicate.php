<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

use NicMart\Generics\Example\Func\CallablePredicate«T»;
use NicMart\Generics\Example\Func\CallablePredicate«FullName»;

use NicMart\Generics\Name\FullName;

$startsWithA = new CallablePredicate«FullName»(function (FullName $name) {
    return substr($name->toString(), 0, 1) == "A";
});

$this->assertTrue(
    $startsWithA(FullName::fromString("A Name"))
);

$this->assertFalse(
    $startsWithA(FullName::fromString("The Name"))
);