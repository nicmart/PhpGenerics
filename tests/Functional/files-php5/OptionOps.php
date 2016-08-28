<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

use NicMart\Generics\Example\PHP5\Option\None«RelativeName»;
use NicMart\Generics\Example\PHP5\Option\None«FullName»;
use NicMart\Generics\Example\PHP5\Option\None«string»;

use NicMart\Generics\Example\PHP5\Option\OptionMap«FullName·RelativeName»;
use NicMart\Generics\Example\PHP5\Option\OptionMap«FullName·string»;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

use NicMart\Generics\Example\PHP5\Option\Some«string»;
use NicMart\Generics\Example\PHP5\Option\Option«string»;
use NicMart\Generics\Example\PHP5\Option\Some«FullName»;

use NicMart\Generics\Example\PHP5\Func\CallableFunction1«FullName·RelativeName»;
use NicMart\Generics\Example\PHP5\Func\CallableFunction1«FullName·Option«RelativeName»»;
use NicMart\Generics\Example\PHP5\Func\CallableFunction1«FullName·Option«string»»;

$option = new Some«FullName»(FullName::fromString("foo"));

$map = new CallableFunction1«FullName·RelativeName»(function (FullName $fullName) {
    return $fullName->toRelative();
});


$optionRelative = OptionMap«FullName·RelativeName»::map(
    $option,
    $map
);

$this->assertEquals(
    RelativeName::fromString("foo"),
    $optionRelative->getOrElse(RelativeName::fromString("NONE"))
);

$noFullName = new None«FullName»();

$noRelativeName = OptionMap«FullName·RelativeName»::map(
    $noFullName,
    $map
);

$this->assertEquals(
    None«RelativeName»::instance(),
    $noRelativeName
);

$flatmap = new CallableFunction1«FullName·Option«string»»(function (FullName $fullName) {
    $str = $fullName->toString();
    if (strlen($str) >= 3) {
        return new Some«string»(substr($str, 0, 3));
    }
    return None«string»::instance();
});

$fullnameForFlatmap1 = FullName::fromString("Abcdefgh");
$fullnameForFlatmap2 = FullName::fromString("Ab");

$first3Letters1 = OptionMap«FullName·string»::flatMap(
    new Some«FullName»($fullnameForFlatmap1),
    $flatmap
);

$this->assertEquals(
    new Some«string»("Abc"),
    $first3Letters1
);

$first3Letters2 = OptionMap«FullName·string»::flatMap(
    new Some«FullName»($fullnameForFlatmap2),
    $flatmap
);

$this->assertEquals(
    None«string»::instance(),
    $first3Letters2
);

$first3Letters3 = OptionMap«FullName·string»::flatMap(
    None«FullName»::instance(),
    $flatmap
);

$this->assertEquals(
    None«string»::instance(),
    $first3Letters3
);
