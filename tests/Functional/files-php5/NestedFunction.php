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

use NicMart\Generics\Example\PHP5\Func\CallableFunction1«Function1«FullName·RelativeName»·Function1«FullName·RelativeName»»;
use NicMart\Generics\Example\PHP5\Func\CallableFunction1«A·B»;
use NicMart\Generics\Example\PHP5\Func\CallableFunction1«B·C»;
use NicMart\Generics\Example\PHP5\Func\CallableFunction1«FullName·RelativeName»;

use NicMart\Generics\Example\PHP5\Func\Function1«FullName·RelativeName»;

use NicMart\Generics\Example\PHP5\Func\Composition«A·B·C»;

use NicMart\Generics\A;
use NicMart\Generics\B;
use NicMart\Generics\C;

$f1 = new CallableFunction1«Function1«FullName·RelativeName»·Function1«FullName·RelativeName»»(function ($x) { return $x; });

$this->assertInstanceOf(
    'NicMart\Generics\Example\PHP5\Func\Function1«Function1«FullName·RelativeName»·Function1«FullName·RelativeName»»',
    $f1
);

$callable1 = new CallableFunction1«FullName·RelativeName»(function () { return RelativeName::fromString("bah"); });
$callable1(FullName::fromString("AAAAA"));
$f1($callable1);

$composite = new Composition«A·B·C»(
    new CallableFunction1«B·C»(function (B $b) { $c = new C; $c->b = $b; return $c; }),
    new CallableFunction1«A·B»(function (A $a) { $b = new B; $b->a = $a; return $b; })
);

$a = new A;
$b = new B;
$b->a = $a;
$c = new C;
$c->b = $b;


$this->assertEquals(
    $c,
    $composite($a)
);