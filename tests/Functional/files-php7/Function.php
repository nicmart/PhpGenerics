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

use NicMart\Generics\Example\PHP7\Func\CallableFunction2«FullName·FullName·FullName»;

use NicMart\Generics\Example\PHP7\Func\CallableFunction1«FullName·RelativeName»;

use NicMart\Generics\Example\PHP7\Func\Apply«FullName·RelativeName»;

use NicMart\Generics\Example\PHP7\Func\CallableUnaryOperator«FullName»;

use NicMart\Generics\Example\PHP7\Func\Functions«FullName»;

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
    $f2(FullName::fromString("a"), FullName::fromString("b")),
    FullName::fromString("a\\b")
);

$apply = new Apply«FullName·RelativeName»();

$this->assertEquals(
    $name->toRelative(),
    $apply($f, $name)
);

// Enfofunc

$endo = new CallableUnaryOperator«FullName»(function (FullName $x) { return $x; });

// Identity

$idOfFull = Functions«FullName»::identity();

$this->assertSame(
    $name,
    $idOfFull($name)
);

$supplierOfName = Functions«FullName»::constant($name);

$this->assertSame(
    $name,
    $supplierOfName()
);

