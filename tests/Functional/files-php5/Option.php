<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

use NicMart\Generics\Example\PHP5\Option\Some«mixed»;
use NicMart\Generics\Example\PHP5\Option\Some«FullName»;
use NicMart\Generics\Example\PHP5\Option\Some«RelativeName»;

use NicMart\Generics\Example\PHP5\Option\None«FullName»;

use NicMart\Generics\Example\PHP5\Option\Option«FullName»;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

$name = FullName::fromString("foo");

$some = new Some«FullName»($name);

$this->assertEquals(
    $name,
    $some->getOrElse(FullName::fromString("bar"))
);

$this->assertTrue(
    $some instanceof Option«FullName»
);

$relative = RelativeName::fromString("foo");

$someRelative = new Some«RelativeName»($relative);

$this->assertEquals(
    $relative,
    $someRelative->getOrElse(RelativeName::fromString("bar"))
);

// Nones

$noFullname = new None«FullName»();

$this->assertEquals(
    $name,
    $noFullname->getOrElse($name)
);

// Types of everything: T -> mixed

$some = new Some«mixed»($name);