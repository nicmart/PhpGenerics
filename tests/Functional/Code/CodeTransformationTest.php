<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Code;


use NicMart\Generics\Autoloader\GenericAutoloaderFactory;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Example\Func\Function1«T1·T2»;
use NicMart\Generics\Example\Func\Function1«RelativeName·FullName»;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\Generic\AngleQuotedGenericName;
use NicMart\Generics\Source\Compiler\GenericCompilerFactory;
use PhpParser\Lexer;
use NicMart\Generics\Example\Option\Some«T»;
use NicMart\Generics\Example\Option\Some«stdClass»;
use NicMart\Generics\Example\Option\Some«Lexer»;
use stdClass;

/**
 * Class CodeTransformationTest
 * @package NicMart\Generics\Code
 */
class CodeTransformationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_transforms_php_code()
    {
        $compiler = GenericCompilerFactory::compiler();
        $generic = AngleQuotedGenericName::fromString(
            'NicMart\Generics\Example\Option\Option«T»'
        );

        $innerType = FullName::fromString("Ns\\MyClass1");

        var_dump($compiler->compile(
            $generic,
            array($innerType)
        ));
    }

    /**
     * @test
     */
    public function it_autoloads()
    {
        GenericAutoloaderFactory::registerAutoloader(
            __DIR__ . "/../../../cache/generics"
        );

        $a = new Some«stdClass»(new stdClass);
        $b = $a->getOrElse(new stdClass);

        $c = new Some«Lexer»(new Lexer());
        $c->type();

        $func = new Function1«RelativeName·FullName»();
    }
}