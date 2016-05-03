<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

use NicMart\Generics\Example\Func\CallableFunction2«T1·T2·T»;
use NicMart\Generics\Example\Func\CallableFunction2«FullName·FullName·FullName»;

use NicMart\Generics\Example\Func\CallableFunction1«T1·T2»;
use NicMart\Generics\Example\Func\CallableFunction1«FullName·RelativeName»;

$f = new CallableFunction1«FullName·RelativeName»(function (FullName $name) {
    return $name->toRelative();
});

$name = FullName::fromString("ahah");

$this->assertEquals(
    $name->toRelative(),
    $f($name)
);

$f2 = new CallableFunction2«FullName·FullName·FullName»(function (FullName $n1, FullName $n2) {
    return $n1->append($n2);
});

$this->assertEquals(
    $f2(Fullname::fromString("a"), FullName::fromString("b")),
    FullName::fromString("a\\b")
);
