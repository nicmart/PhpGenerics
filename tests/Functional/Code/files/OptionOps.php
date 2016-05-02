<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

use NicMart\Generics\Example\Option\None«T»;
use NicMart\Generics\Example\Option\None«RelativeName»;
use NicMart\Generics\Example\Option\None«FullName»;

use NicMart\Generics\Example\Option\OptionMap«S·T»;
use NicMart\Generics\Example\Option\OptionMap«FullName·RelativeName»;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

use NicMart\Generics\Example\Option\Some«T»;
use NicMart\Generics\Example\Option\Some«FullName»;

use NicMart\Generics\Example\Option\Option«T»;
use NicMart\Generics\Example\Option\Option«FullName»;

$option = new Some«FullName»(FullName::fromString("foo"));

$map = function (FullName $fullName) {
    return $fullName->toRelative();
};

$optionRelative = OptionMap«FullName·RelativeName»::map(
    $option,
    $map
);

$this->assertEquals(
    $optionRelative->getOrElse(RelativeName::fromString("boo")),
    RelativeName::fromString("foo")
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